<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\ContactType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,MailerInterface $mailer): Response
    {
        $form=$this->createForm(ContactType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $contact=$form->getData();
            $email = (new Email())
                ->From($contact['email'])
                ->getTo('bechir.jlidi@esprit.tn')
                ->to(array(setbody($this->renderView(
                    'email/contact.html.twig', compact('contact')
                ))), 'text/html'
                );
                //->text('Sending emails is fun again!')


            $mailer->send($email);
            $this->addFlash('message','le message a bien ete envoye');
        }
        return $this->render('contact/index.html.twig', [
            'ContactForm' => $form->createView()
        ]);
    }
}
