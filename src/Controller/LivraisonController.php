<?php

namespace App\Controller;
use App\Entity\Commande;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\ColumnChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Livraison;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\LivraisonformType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Mime\Email;
class LivraisonController extends AbstractController
{
    /**
     * @Route("/admin/livraison", name="livraison")
     */
    public function index(): Response
    {
        return $this->render('livraison/index.html.twig', [
            'controller_name' => 'LivraisonController',
        ]);
    }
    /**
     * @Route("/admin/affiche", name="affiche")
     */
    public function affiche()
    {
        $livraison = $this->getDoctrine()->getRepository(livraison::class)->findAll();

        return $this->render('livraison/read.html.twig', ["livraison" => $livraison ]);
    }
    /**
     * @Route("/afficher1", name="afficher1")
     */
    public function afficher()
    {
        $livraison = $this->getDoctrine()->getRepository(livraison::class)->listOrderByDate();;

        return $this->render('Livraison/read.html.twig', ["livraison" => $livraison ]);
    }
    /**
     * @Route("/afficher", name="afficher")
     */
    public function afficherNom()
    {
        $livraison = $this->getDoctrine()->getRepository(livraison::class)->listOrderByNom();;

        return $this->render('Livraison/read.html.twig', ["livraison" => $livraison ]);
    }
    /**
     * @Route("/delet/{id}", name="b")
     */
    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        $entityManager->remove($livraison);
        $entityManager->flush();

        return $this->redirectToRoute('affiche');
    }

    /**
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     * @Route ("/admin/ajoutt", name="add")
     */
    function Add(Request $request ,MailerInterface $mailer)
    {
        $livraison = new livraison();
        $form = $this->createForm(LivraisonformType::class, $livraison);
        $form->add('Ajouter', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraison->setStatut("En cours");
            $em = $this->getDoctrine()->getManager();
            $em->persist($livraison);
            $em->flush();

            $email = (new Email())
                ->from('bechir.jlidi@esprit.tn')
                ->to((String)$livraison->getCommande()->getEmailuser())
                ->priority(Email::PRIORITY_HIGH)
                ->subject('[Paradise] Traitement de livraison !')
                //->text('Sending emails is fun again!')
                ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Votre livraison est bien passée.  votre commande sera livré le '.($livraison->getDateLiv()->format('Y/m/d ')).' avec le livreur' .$livraison->getLivreur().'</p><br>');
            $mailer->send($email);
            return $this->redirectToRoute('affiche');

        }
        return $this->render('Livraison/ajouter.html.twig', [
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/modify/{id}", name="modify")
     */
    public function modifier (Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        $form = $this->createForm(LivraisonformType::class, $livraison);
        $form->add('modifier', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('affiche');
        }

        return $this->render("Livraison/modifier.html.twig", [

            "form" => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/imprimer", name="livraison_liste")
     */
    public function listel()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $livraison = $this->getDoctrine()->getRepository(livraison::class)->findAll();



        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('Livraison/test.html.twig', ["livraison" => $livraison]);

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
     * @Route("/statut/{id}", name="S")
     * @param int $id
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function Statut(int $id,MailerInterface $mailer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        $livraison->setStatut("Terminer");
        $entityManager->flush();
        $email = (new Email())
            ->from('bechir.jlidi@esprit.tn')
            ->to((String)$livraison->getCommande()->getEmailuser())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('[Paradise] Traitement de livraison !')
            //->text('Sending emails is fun again!')
            ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>votre commande a été expédiée  </p><br>
                   <p>Merci pour votre confiance. </p><br>');
        $mailer->send($email);

        return $this->redirectToRoute('affiche');
    }
    /**
     * @Route("/admin/livraisonstat", name="livraisonstat")
     */
    public function livraisontat(): Response
    {

        $em=$this->getDoctrine()->getManager();
        $data = $em->getRepository(Livraison::class)->findTotalLivraison();

        $d = array(['Nom Livreur', 'Nombre Total des livraisons'],
        );
        foreach ($data as $res)
        {

            array_push($d,[$res['NomLiv'],$res['TotalLivraison']]);
        }








        $chart = new ColumnChart();
        $chart->getData()->setArrayToDataTable($d);

        $chart->getOptions()->getChart()
            ->setTitle('Nombre de livraison par livreur');

        $chart->getOptions()
            ->setBars('vertical')
            ->setHeight(400)
            ->setWidth(900)
            ->setColors(['#7570b3', '#d95f02', '#7570b3'])
            ->getVAxis()
            ->setFormat('decimal');


        return $this->render('livraison/statlivraison.html.twig', [
            'chart'=>$chart

        ]);
    }



}
