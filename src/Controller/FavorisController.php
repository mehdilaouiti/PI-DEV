<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    /**
     * @Route("/favoris", name="favoris")
     */
    public function index(): Response
    {
        return $this->render('favoris/index.html.twig', [
            'controller_name' => 'FavorisController',
        ]);
    }

    /**
     * @Route("/favoris/ajout/{id}", name="ajout_favoris")
     */
    public function ajoutFavoris(Article $article)
    {
        if (!$article) {
            throw new NotFoundHttpException('Pas d\'article trouvé');
        }
        $article->addFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->redirectToRoute('article');
    }

    /**
     * @Route("/favoris", name="favoris")
     */
    public function readFavoris()
    {
        $favoris = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('article/favoris.html.twig', [
            'article' => $favoris
        ]);

    }
    /**
     * @Route("/favoris/delete/{id}", name="delete_favoris")
     */
    public function DeleteFavoris(Article $article)
    {
        if(!$article){
            throw new NotFoundHttpException('Pas d\'article trouvé');
        }
        $article->removeFavori($this->getUser());

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->redirectToRoute('favoris');
    }
}
