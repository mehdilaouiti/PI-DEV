<?php

namespace App\Controller;
use App\Entity\Article;
use App\Entity\Fabricant;
use App\Entity\Panier;
use App\Form\FabricantType;
use App\Form\PanierType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;


class PanierController extends AbstractController
{

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkoutCartJson( SessionInterface $session ,ArticleRepository $articleRepository)
    {
        $panier = $session->get('panier', []);
        $panierwithdata = [];
        $em=$this->getDoctrine()->getManager();

        return $this->redirectToRoute('panier');

        foreach ($panier as $id => $quantity) {
            $add_panier=new Panier();
            $add_panier->setClient(1);
            $add_panier->setTotal("1220");
            $em->persist($add_panier);
            $em->flush();
            unset($panier[$id]);
        }
        return $this->redirectToRoute('panier');

    }
    /**
     * @Route("/cart/add.json", name="add_cart_json", methods={"POST","GET"})
     */
    public function addCartJson( SessionInterface $session ,ArticleRepository $articleRepository)
    {
        $panier = $session->get('panier', []);
        $panierwithdata = [];
        $total=0;
        if($_POST["product_id"]) {
            if (!empty($panier[$_POST["product_id"]])) {
                $panier[$_POST["product_id"]]++;
            } else {
                $panier[$_POST["product_id"]] = 1;
            }
            $session->set('panier', $panier);
        }


        foreach ($panier as $id => $quantity) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $panierwithdata[] = [
                'id'=>$repositoryArticle->getId(),
                'name' => $repositoryArticle->getNom(),
                'price' => $repositoryArticle->getPrix(),
                'Produit-somme'=>$repositoryArticle->getPrix()*$quantity,
                'pictureUrl' => $repositoryArticle->getImg(),
                'quantity'=>$quantity
            ];
        }
        foreach ($panierwithdata as $panierd) {
            $prix=$panierd['price']*$panierd['quantity'];
            $total+=$prix;

        }

        $hamza=count($panierwithdata);
        return new JsonResponse( ["list"=>$panierwithdata,"total"=>$total,"nbrpanier"=>$hamza]);
    }


    /**
     * @Route("/increasePanier", name="increasePanier")
     */
    public function increasePanier($id, SessionInterface $session){
        $panier = $session->get('panier',[]);
        if(!empty($panier[$_POST["product_id"]])){
            $panier[$_POST["product_id"]]++;
        }
        else{
            $panier[$_POST["product_id"]] = 1;
        }
        $session->set('panier', $panier);
        return new JsonResponse( ["list"=>"ok"]);
    }


    /**
     * @Route("/cart/view.json", name="view_cart_json", methods={"POST","GET"})
     */
    public function viewCartJson( SessionInterface $session ,ArticleRepository $articleRepository)
    {
        $panier = $session->get('panier', []);
        $panierwithdata = [];
        $total=0;


        foreach ($panier as $id => $quantity) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $panierwithdata[] = [
                'id'=>$repositoryArticle->getId(),
                'name' => $repositoryArticle->getNom(),
                'price' => $repositoryArticle->getPrix(),
                'pictureUrl' => $repositoryArticle->getImg(),
                'quantity'=>$quantity
            ];
        }
        foreach ($panierwithdata as $panierd) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $prix=$repositoryArticle->getPrix()*$panierd['quantity'];
            $total+=$prix;

        }
        $hamza=count($panierwithdata);
        return new JsonResponse( ["list"=>$panierwithdata,"total"=>$total,"nbrpanier"=>$hamza]);

    }

    /**
     * @Route("/cart/remove.json", name="remove_cart_json", methods={"POST","GET"})
     */
    public function removeCartJson( SessionInterface $session ,ArticleRepository $articleRepository)
    {

        $panier = $session->get('panier', []);
        $panierwithdata = [];
        $total=0;
        if(!empty($panier[$_POST["product_id"]])){
            unset($panier[$_POST["product_id"]]);
        }
        $session->set('panier',$panier);
        foreach ($panier as $id => $quantity) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $prix=$repositoryArticle->getPrix()*$quantity;
            $total+=$prix;

        }

        foreach ($panier as $id => $quantity) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $panierwithdata[] = [
                'id'=>$repositoryArticle->getId(),
                'name' => $repositoryArticle->getNom(),
                'price' => $repositoryArticle->getPrix(),
                'pictureUrl' => $repositoryArticle->getImg(),
                'quantity'=>$quantity
            ];
        }
        $hamza=count($panierwithdata);
        return new JsonResponse( ["list"=>$panierwithdata,"total"=>$total,"nbrpanier"=>$hamza]);

    }

    /**

     * @Route("/panier", name="panier")
     */
    public function readPanier(SessionInterface $session,ArticleRepository $articleRepository)
    {
        $panier=$session->get('panier',[]);
        $panierwithdata=[];
        $total=[];
        foreach ($panier as $id=>$quantity){

            $panierwithdata[]=[
                'product'=>$articleRepository->find($id),
                'quantity'=>$quantity
            ];
        }
        foreach ($panierwithdata as $panierd) {

            $total[]=[
                'produit'=>$panierd['product'],
                'prixtotal'=> $panierd['product']->getPrix() * $panierd['quantity'],
                'quantite'=>$panierd['quantity'],
                'prix'=>$panierd['product']->getPrix()
            ];

        }
        return $this->render('panier/read.html.twig', ['panier' =>$total ]);
    }



    /**
     * @Route("/panier/ajouter{id}", name="createpanier")
     */

    public function createPanier ($id ,Request $request)
    {
        $session=$request->getSession();
        $panier =$session->get('panier',[]);
        $panier[$id]=1;
        $session->set('panier',$panier);
        dd($session->get('panier'));

    }

    /**
     * @Route("/panier/update/{idpanier}", name="updatepanier")
     */

    public function updateArticle(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository(Panier::class);
        $panier = $repo->find($id);
        $form = $this->createForm(PanierType::class);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute(readpanier);
        }
        return $this->render('panier/base.html.twig', [
            'form' => $form->createView()]);
    }

    /**
     * @Route("/panier/delete/{id}", name="deletepanier")
     */

    public function deletePanier($id,SessionInterface $session)
    {
        $panier=$session->get('panier',[]);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier',$panier);

        return $this->redirectToRoute('panier');
    }








    /**
     * @Route("/panieradmin", name="readpanadmin")
     */
    public function readPanieradmin()
    {
        $repo = $this->getDoctrine()->getRepository(Panier::class);
        $listPanier = $repo->findAll();

        return $this->render('admin/panier/read.html.twig', [
            'panier' => $listPanier]);
    }

    /**
     * @Route("/panieradmin/ajouter", name="createpanadmin")
     */

    public function createPanierAdmin(Request $request)
    {
        $panier = new Panier();
        $form = $this->createForm(PanierType::class, $panier);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($panier);
            $em->flush();
            return $this->redirectToRoute('readpanadmin');
        }
        return $this->render('admin/panier/create.html.twig', [
            'form' => $form->createView()]);

    }

    /**
     * @Route("/panieradmin/modifier/{id}", name="updatepanadmin")
     */

    public function updateArticleAdmin(Request $req, $id)
    {
        $repo = $this->getDoctrine()->getRepository(Panier::class);
        $panier = $repo->find($id);
        $form = $this->createForm(PanierType::class, $panier);
        $form->add('Modifier', SubmitType::class);
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('readpanadmin');
        }
        return $this->render('/admin/panier/update.html.twig', [
            'form' => $form->createView()]);
    }

    /**
     * @Route("/panieradmin/supprimer/{id}", name="deletepanadmin")
     */

    public function deletePanierAdmin( $id)
    {
        $em = $this->getDoctrine()->getManager();
        $panier=$em->getRepository(Panier::class)->find($id);
        $em->remove($panier);
        $em->flush();
        return $this->redirectToRoute('readpanadmin');

    }


    /**
     * @Route("/imprimerFact", name="imprimer_facture")
     */
    public function listel(SessionInterface $session ,ArticleRepository $articleRepository)
    {

        $panier=$session->get('panier',[]);
        $panierwithdata=[];
        $panierdata=[];
        $total=[];
        $totall=0;
        foreach ($panier as $id=>$quantity){

            $panierwithdata[]=[
                'product'=>$articleRepository->find($id),
                'quantity'=>$quantity
            ];
        }
        foreach ($panierwithdata as $panierd) {

            $total[]=[
                'produit'=>$panierd['product'],
                'prixtotal'=> $panierd['product']->getPrix() * $panierd['quantity'],
                'quantite'=>$panierd['quantity'],
                'prix'=>$panierd['product']->getPrix()
            ];

        }

        foreach ($panier as $id => $quantity) {
            $repositoryArticle = $this->getDoctrine()->getRepository(Article::class)->find($id);
            $panierdata[] = [
                'id'=>$repositoryArticle->getId(),
                'name' => $repositoryArticle->getNom(),
                'price' => $repositoryArticle->getPrix(),
                'Produit-somme'=>$repositoryArticle->getPrix()*$quantity,
                'pictureUrl' => $repositoryArticle->getImg(),
                'quantity'=>$quantity
            ];
        }
        foreach ($panierdata as $panierd) {
            $prix=$panierd['price']*$panierd['quantity'];
            $totall+=$prix;

        }



        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $subtotal=($totall*12)/100;
        $totalnet=$totall+$subtotal;


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Facture/FactureResult.html.twig', ["panier" => $total,"total"=>$totall,"totalnet"=>$totalnet,"subtotal"=>$subtotal]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $session->remove('panier');
        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("facture1.pdf", [
            "Attachment" => true
        ]);


    }
    /*public function listel(SessionInterface $session ,ArticleRepository $articleRepository)
{
    // Configure Dompdf according to your needs
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');

    // Instantiate Dompdf with our options
    $dompdf = new Dompdf($pdfOptions);

    $panier=$session->get('panier',[]);
    $panierwithdata=[];
    foreach ($panier as $id=>$quantity){

        $panierwithdata[]=[
            'product'=>$articleRepository->find($id),
            'quantity'=>$quantity
        ];
    }
*/



}
