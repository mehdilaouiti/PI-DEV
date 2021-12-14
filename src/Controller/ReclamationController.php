<?php

namespace App\Controller;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\ReclamationType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Psr7\UploadedFile;
use Symfony\Component\HttpFoundation\JsonRespImageonse;

class ReclamationController extends AbstractController
{
    /**
     * @Route("/admin/afficherreclamation", name="afficherreclamation")
     */
    public function afficherReclamationAction() : Response
    {
        $reclamations=$this->getDoctrine()->getRepository(Reclamation::class);
        $reclamations=$reclamations->findAll();
        return $this->render('reclamation/afficherReclamation.html.twig', array ('reclamations'=>$reclamations));
    }

    /**
     * @Route("/ajouterreclamation", name="ajouterreclamation")
     * @param Request $request
     * @throws TransportExceptionInterface
     * @param MailerInterface $mailer
     * @return Response
     */
    public function ajouterReclamationAction(Request $request,MailerInterface $mailer)
    {


        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->findAll();
        $reclamations =new reclamation();
        $form =$this->createForm(ReclamationType::class, $reclamations);
        $form =$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $file
             */
            $file = $form->get('img')->getData();
            $fileName = bin2hex(random_bytes(6)).'.'.$file->guessExtension();
            $file->move ($this->getParameter('images_directory'),$fileName);
            $reclamations->setImg("http://127.0.0.1:8000/upload/" .$fileName);
            $date=new \DateTime() ;
            $reclamations->setDate_rec($date);
            $em = $this->getDoctrine()->getManager();
            $user=$this->getUser();
            $reclamations->setUser($user);
            $em->persist($reclamations);
            $em->flush();

            $email = (new Email())
                ->from('para.diseesprit@gmail.com')
                ->to('para.diseesprit@gmail.com')
                ->priority(Email::PRIORITY_HIGH)
                ->subject('[Paradise] Nouvelle Réclamation !')
                //->text('Sending emails is fun again!')
                ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Une nouvelle réclamation a été envoyée de type&nbsp;'.$reclamations->getType().'&nbsp;et de description comme suit :&nbsp; '.($reclamations->getDescription()).' de la part de&nbsp;'.($reclamations->getUser()->getName()) . '&nbsp;.</p><br>');
            $mailer->send($email);
            $email = (new Email())
                ->from('para.diseesprit@gmail.com')
                ->to($reclamations->getUser()->getEmail())
                ->priority(Email::PRIORITY_HIGH)
                ->subject('[Paradise] Réclamation !')
                //->text('Sending emails is fun again!')
                ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Votre réclamation a été envoyée avec succés.</p>');
            $mailer->send($email);}



        return $this->render('reclamation/ajouterReclamation.html.twig',  [
            "form_title" => "Ajouter une reclamation",
            "form_reclamation" => $form->createView(),
        ]);
    }

    public function modifierReclamationAction(Request $request,$id)
    {

        $em=$this->getDoctrine()->getManager();
        $e=$em->getRepository('reclamationBundle:reclamation')->find($id);
        $form=$this->createForm(reclamationType::class,$e);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $em=$this->getDoctrine()->getManager();
            $em->persist($e);
            $em->flush();
            return $this->redirectToRoute("afficherReclamation");
        }
        return $this->render('@reclamation/view/modifierReclamation.html.twig', array('f'=>$form->createView()));
    }
    public function supprimerReclamationAction($id)
    {

        $em=$this->getDoctrine()->getManager();
        $reclamations= $em->getRepository(reclamation::class)->find($id);
        $em->remove($reclamations);
        $em->flush();
        return $this->redirectToRoute("afficherReclamation");
    }
    /**
     * @Route("/rechercheReclamation", name="rechercheReclamation")
     * @param Request $request
     * @return Response
     */
    public function rechercheReclamationAction(Request $request)
    {
        $reclamation=new reclamation();
        $form=$this->createForm(reclamationType::class);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $reclamation=$this->getDoctrine()->getRepository(reclamation::class)->findBy(array('reclamation'=>$reclamation->getId()));}
        else {

            $reclamation=$this->getDoctrine()->getRepository(reclamation::class)->findAll();
        }
        return $this->render('@reclamation/view/rechercherReclamation.html.twig',array("form"=>$form->createView()
        ,'reclamations'=>$reclamation));
    }
    /**
     * @Route("/imprimerReclamation", name="reclamation_liste")
     */
    public function imprimerreclamation()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $reclam = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();


        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation/test.html.twig', ["reclamations" => $reclam]);

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
     * @Route("/admin/statReclamation", name="statreclamation")
     */
    public function chartAction()
    {
        $pieChart = new PieChart();
        $em= $this->getDoctrine();
        $type= $em->getRepository(Reclamation::class)->findTotalReclamation();


        $data= array();
        $stat=['type', 'date_rec'];
        $nb=0;
        array_push($data,$stat);

        foreach($type as $t)
        {
            $em= $this->getDoctrine();

            $nb= $em->getRepository(Reclamation::class)->findOneBy(array('type'=>$t));
            //array('data'=>sizeof($nb));
            if($nb!=null)
            {  $stat=array();
                array_push($stat,$t->getType(),$nb->getDate_rec());
                $stat=[$t->getType,$nb->getDate_rec()];
                array_push($data,$stat);}

        }

        $pieChart->getData()->setArrayToDataTable($data);

        $pieChart->getOptions()->setTitle('');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        return $this->render('reclamation/stat.html.twig', array('piechart' => $pieChart));

    }
}



