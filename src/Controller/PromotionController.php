<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Livraison;
use App\Entity\Materiel;
use App\Entity\MaterielType;
use App\Entity\Promotion;
use App\Entity\User;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\HttpFoundation\JsonRespImageonse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;


class PromotionController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {   $promotion=$this->getDoctrine()->getRepository(Promotion::class)->findAll();
        $article=$this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('home.html.twig',['article'=>$article,'promotion'=>$promotion]);
    }

    /**
     * @Route("/admin/promotion/{id}", name="promo")
     */
    public function indexP($id): Response
    {
        $promotion=$this->getDoctrine()->getRepository(Promotion::class)->find($id);
        if(!$promotion){
            throw $this->createNotFoundException('Aucun produit ne correspond à l\' id '.$id);
        }
        return $this->render('promotion/show.html.twig', [
            'promotion' =>$promotion
        ]);
    }
    /**
     * @Route("/promotions/{id}", name="showpromo")
     */
    public function showprom($id): Response
    {
        $promotion=$this->getDoctrine()->getRepository(Promotion::class)->find($id);
        if(!$promotion){
            throw $this->createNotFoundException('Aucun produit ne correspond à l\' id '.$id);
        }
        return $this->render('promotion/showprom.html.twig', [
            'promotion' =>$promotion
        ]);
    }

    /**
     * @Route ("/admin/recherchepromotion",name="recherchepromo")
     */
    public function recherche(PromotionRepository $repository , Request $request , PaginatorInterface $paginator )
    {
        $data=$request->get('search');
        $promotion=$repository->SearchName($data);

        $promotion = $paginator->paginate(
            $promotion, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3) ;/*limit per page*/

        return $this->render('admin/promotion/admin.html.twig', array(
            'promotion' => $promotion));

    }

    /**
     * @Route("/admin/listtriepromo", name="listtriepromo")
     */
    public function listTriepromo(PaginatorInterface $paginator , Request $request)
    {
        $ar = $this->getDoctrine()->getRepository(Promotion::class)->listOrderByRemise();
        $promotion = $paginator->paginate(
            $ar, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3) ;/*limit per page*/

        return $this->render('admin/promotion/admin.html.twig', [
            'promotion' => $promotion]);



    }

    /**
     * @Route("/admin/promotion", name="promotion")
     */
    public function readPromotion(PaginatorInterface $paginator , Request $request): Response
    { $repo=$this->getDoctrine()->getRepository(Promotion::class);
        $listePromo=$repo->DateExpr();

        $listePromo = $paginator->paginate(
            $listePromo, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            3) ;/*limit per page*/

        return $this->render('admin/promotion/admin.html.twig', array(
            'promotion' => $listePromo));


    }
    /**
     * @Route("/promotions", name="h_promotion")
     */
    public function readPromotions(): Response
    { $repo=$this->getDoctrine()->getRepository(Promotion::class);
        $listePromo=$repo->DateExpr();

        return $this->render('promotion/index.html.twig',[
            'promotion'=>$listePromo]);

    }




    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     * @Route("/admin/promotion/test/ajouter", name="new")
     */
    public function createPromotion(Request $request,MailerInterface $mailer)
    {   $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->findAll();
        $promotion=new Promotion();
        $form=$this->createForm(PromotionType::class , $promotion);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('img')->getData();
            $fileName = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
            $file->move ($this->getParameter('images_directory'),$fileName);
            $promotion->setImg("http://127.0.0.1:8000/upload/" . $fileName);

            $em->persist($promotion);
            $em->flush();
            foreach ($user as $u)
            {
                $email = (new Email())
                    ->from('para.diseesprit@gmail.com')
                    ->to($u->getEmail())
                    ->priority(Email::PRIORITY_HIGH)
                    ->subject('[Paradise] Nouvelle Promotion !')
                    //->text('Sending emails is fun again!')
                    ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Une nouvelle promotion est bien lancée.  votre commande sera livré le '.($promotion->getNompromotion()).' avec le livreur' .$promotion->getDescription().'</p><br>');
                $mailer->send($email);}
            return $this->redirectToRoute('promotion');

        }

        return $this->render('admin/promotion/ajouter.html.twig',[
            'form'=>$form->createView()]);

    }
    /**
     * @param Request
     * @return Response
     * @Route("/admin/promotion/update/{id}" , name="u")
     */
    public function updatePromotion(Request $request,$id){
        $repo=$this->getDoctrine()->getRepository(Promotion::class);
        $promo=$repo->find($id);
        $form=$this->createForm(PromotionType::class,$promo);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('img')->getData();
            $fileName = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
            $file->move ($this->getParameter('images_directory'),$fileName);
            $promo->setImg("http://127.0.0.1:8000/upload/" .$fileName);
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('promotion');
        }
        else{
            return $this->render('admin/promotion/modifier.html.twig',[
                'form'=>$form->createView()]);
        }
    }
    /**
     * @Route("/admin/promotion/delete/{id}",name="d")
     */
    public function deletePromotion(int $id): Response
    {
        $em=$this->getDoctrine()->getManager();
        $repo=$this->getDoctrine()->getRepository(Promotion::class);
        $promo=$repo->find($id);
        $em->remove($promo);
        $em->flush();

        return $this->redirectToRoute('promotion');

    }
    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     * @Route("/admin/promotion/test/rappel/{id}", name="rappel")
     */
    public function rappelPromotion(MailerInterface $mailer,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Promotion::class);
        $user = $em->getRepository(User::class)->findAll();
        $promo = $repo->find($id);


        foreach ($user as $u) {
            $email = (new Email())
                ->from('para.diseesprit@gmail.com')
                ->to($u->getEmail())
                ->priority(Email::PRIORITY_HIGH)
                ->subject('[Paradise] Rappel Promotion ' . ($promo->getNompromotion()) . '!')
                //->text('Sending emails is fun again!')
                ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Cette promotion est toujours validé. Venez avant que la promotion se termine.</p> <br>
                   Venez avant que la promotion <span style="color: rgb(226, 80, 65);"><strong>se termine</strong></span> <strong>!!!</strong><br><br> MERCI. ');
            $mailer->send($email);
        }
        return $this->redirectToRoute('promotion');


    }
    /**
     * @Route("/imprimerPromotion", name="promotion_liste")
     */
    public function imprimerpromo()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $promo = $this->getDoctrine()->getRepository(Promotion::class)->findAll();


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Promotion/test.html.twig', ["promotion" => $promo]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);

    }

    /**
     * @Route("/allPromoJSON", name="allPromoJSON")
     */
    public function AllPromo(NormalizerInterface $Normalizer,Request $request)
    {
        $repo=$this->getDoctrine()->getRepository(Promotion::class);
        $promo=$repo->findAll();
        $jsonContent = $Normalizer->normalize($promo , 'json',['groups'=>'post:read']);
        return new Response(json_encode($jsonContent));
    //return $this->render('promotion/promotion.html.twig',['data'=>$jsonContent]);
    }



    /**
     * @Route("/addPromoJSON/new", name="addPromoJSON")
     */
    public function addPromoJSON(Request $request,NormalizerInterface $Normalizer) {

    $em = $this->getDoctrine()->getManager();
    $promo = new Promotion();
        $promo->setNompromotion($request->get('nompromotion'));
        $promo->setRemise($request->get('remise'));
        $promo->setDescription($request->get('description'));
    $em->persist($promo);
    $em->flush();
    $jsonContent = $Normalizer->normalize($promo, 'json',['groups'=>'post:read']);
    return new Response(json_encode($jsonContent));
}
    /**
     * @Route("/allPromoJSON/{id}", name="PromoJSONId")
     */
    public function PromId(Request $request,$id, Normalizerinterface $Normalizer){
        $em = $this->getDoctrine()->getManager();
        $promo=$em->getRepository(Promotion::class)->find($id);
        $jsonContent = $Normalizer->normalize($promo,'json',['groups'=>'post:read']);
return new Response(json_encode($jsonContent)); }

}
