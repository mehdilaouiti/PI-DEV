<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Form\ArticleType;
use App\Form\SearchForm;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\HttpFoundation\JsonRespImageonse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ArticleController extends AbstractController
{
    /**
     * @Route("/a", name="z")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/article", name="article")
     */
    public function readArticle( ArticleRepository $repository,Request $request): Response
    {
        $data = new SearchData();
        $data->page=$request->get('page',1);
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $articles=$repository->findSearch($data);
        return $this->render('article/read.html.twig', [
            'art' => $articles,
            'form' => $form->createView()
        ]);

    }
    /**
     * @Route ("/recherchearticle",name="recherchearticle")
     */
    public function recherche(ArticleRepository $repository , Request $request)
    {
        $data=$request->get('search');
        $article=$repository->SearchName($data);
        $repo=$this->getDoctrine()->getRepository(Categorie::class);
        $listecat=$repo->findAll();
        return $this->render('article/recherchenav.html.twig',array('article'=>$article,'cat' => $listecat));
    }
    /**
     * @Route("/art/{id}", name="art")
     */
    public function afficheart($id): Response
    {
        $article=$this->getDoctrine()->getRepository(Article::class)->find($id);
        if(!$article){
            throw $this->createNotFoundException('Aucun produit ne correspond à l\' id'.$id);
        }
        $repo=$this->getDoctrine()->getRepository(Categorie::class);
        $listecat=$repo->findAll();
        return $this->render('article/afficheart.html.twig', [
            'article' =>$article,'cat' => $listecat
        ]);
    }










    /**
     * @Route("/admin/article", name="readartadmin")
     */
    public function readArticleAdmin(): Response
    {
        $repo=$this->getDoctrine()->getRepository(Article::class);
        $listArticle =$repo->findAll();

        return $this->render('admin/article/read.html.twig', [
            'article' => $listArticle]);
    }
    /**
     * @Route("/admin/ajouter_article", name="createartadmin" ,methods={"GET","POST"})
     */

    public function createArticleAdmin (Request $req){
        $article=new Article();
        $form=$this->createForm(ArticleType::class,$article);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('img')->getData();
            $fileName = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
            $file->move ($this->getParameter('images_directory'),$fileName);
            $article->setImg("http://127.0.0.1:8000/upload/" .$fileName);
            //$article->setImg($fileName);
            $em=$this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('readartadmin');
        }
        return $this->render('admin/article/create.html.twig', [
            'form' => $form->createView()]);

    }
    /**
     * @Route("/admin/update_article/{id}", name="updateartadmin")
     */

    public function updateArticleAdmin(Request $req,$id){
        $repo=$this->getDoctrine()->getRepository(Article::class);
        $article=$repo->find($id);
        $form=$this->createForm(ArticleType::class,$article);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('img')->getData();
            $fileName = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
            $file->move($this->getParameter('images_directory'),$fileName);
            $article->setImg("http://127.0.0.1:8000/upload/" .$fileName);
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('readartadmin');
        }
        return $this->render('/admin/article/update.html.twig', [
            'form' => $form->createView()]);
    }
    /**
     * @Route("/admin/delete_article/{id}", name="deleteartadmin")
     */

    public function deleteArticleAdmin ($id){
        $em = $this->getDoctrine()->getManager();
        $article=$em->getRepository(Article::class)->find($id);
        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute('readartadmin');
    }
    /**
     * @Route("/admin/triNomAsc_article}", name="trinomasc")
     */
    public function triNomASC()
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->listOrderByNomASC ();

        return $this->render('admin/article/read.html.twig', ["article" => $article ]);
    }
    /**
     * @Route("/admin/triNomDesc_article}", name="trinomdesc")
     */
    public function triNomDESC()
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->listOrderByNomDESC ();

        return $this->render('admin/article/read.html.twig', ["article" => $article ]);
    }


    /**
     * @Route("/allArtJSON", name="allArtJSON")
     */
    public function AllPromo(NormalizerInterface $Normalizer,Request $request)
    {
        $repo=$this->getDoctrine()->getRepository(Article::class);
        $promo=$repo->findAll();
        $jsonContent = $Normalizer->normalize($promo , 'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
        //return $this->render('promotion/promotion.html.twig',['data'=>$jsonContent]);
    }


}
