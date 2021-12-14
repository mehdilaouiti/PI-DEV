<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Promotion;
use App\Form\CommandeType;
use App\Repository\ArticleRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CommandeController extends AbstractController
{
    /**
     * @Route("/admin/commande", name="comman")
     */
    public function readPromotion(Request $request): Response
    { $repo=$this->getDoctrine()->getRepository(Commande::class);
        $listeCom=$repo->findAll();



        return $this->render('Commande/AfficheCommande.html.twig', array(
            'commande' => $listeCom));


    }


    /**
     * @Route("/Ajoutercommande", name="Addcommande")
     */
    public function AddCommande(Request $request,ValidatorInterface $validator,SessionInterface $session, ArticleRepository $produitRepository): Response
    {
        $panier = $session->get('panier');
        $panierwithData = [];
        foreach ($panier as $id => $quantite){

            $panierwithData[] = [
                'produit' => $produitRepository->find($id),
                'quantite' => $quantite
            ];


        }



        $errors = null;
        dump($panierwithData);
        $form = $this->createForm(CommandeType::class);
        $form= $form->handleRequest($request);

        // $Panier = $em->getRepository(Panier::class)->find(1)
        // boucle : somme
        $total_panier = 750;
        $em=$this->getDoctrine()->getManager();
        //  $user = $em->getRepository(User::class)->find(1);
        $user = $this->get('security.token_storage')->getToken()->getUser();

        // Static apres avec Panier
        /*  $produit = $em->getRepository(Produit::class)->find(1);
          dump($produit);*/
        $total=0;
        foreach ($panierwithData as $panierd) {
            $totalPanier = $panierd['produit']->getPrix() * $panierd['quantite'];
            $total += $totalPanier;
        }
        if ($form->isSubmitted())
        {

            foreach ($panierwithData as $panierd) {
                $commande = new Commande();
                $commande->setNom($request->request->get('commande')['nom']);
                $commande->setPrenom($request->request->get('commande')['prenom']);
                $commande->setAdresse($request->request->get('commande')['adresse']);
                $commande->setDescriptionAdresse($request->request->get('commande')['descriptionAdresse']);
                $commande->setGouvernorat($request->request->get('commande')['gouvernorat']);
                $commande->setCodePostal((int)$request->request->get('commande')['codePostal']);
                $commande->setNumeroTelephone((int)$request->request->get('commande')['numeroTelephone']);
                $commande->setArticles($panierd['produit']);
                $commande->setPrixTotal($total);

                $commande->setEmailuser($user);
                $errors = $validator->validate($commande);
                dump($errors);
                if(count($errors)>0)
                {
                    return $this->render('Commande/AjouterCommande.html.twig', ['user'=>$user,
                        'form' => $form->createView(),
                        'errors'=> $errors,
                        'total'=>$total,
                        'panierD' => $panierwithData


                    ]);
                }

                $em->persist($commande);
                $em->flush();


            }



            /*$email = (new Email())
                ->from('anis.hajali@esprit.tn')
                ->to('omar.trabelsi.1@esprit.tn')
                //->attachFromPath('/path/to/documents/terms-of-use.pdf')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Bonjour nom_User \n Votre commande de : \n Produit 1 , produit 2 est passé avec succees ')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);*/

            return $this->redirectToRoute('facture');

        }



        return $this->render('Commande/AjouterCommande.html.twig', ['user'=>$user,
            'form' => $form->createView(),
            'total'=>$total,
            'panierD' => $panierwithData


        ]);


    }


    /**
     * @Route("/facture", name="facture")
     */
    public function facture( Request $request, ValidatorInterface $validator, SessionInterface $session, ArticleRepository $articleRepository)
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
        $subtotal=($totall*12)/100;
        $totalnet=$totall+$subtotal;
        return $this->render('Facture/FactureResult.html.twig', ["panier" => $total,"total"=>$totall,"totalnet"=>$totalnet,"subtotal"=>$subtotal]);
    }


}