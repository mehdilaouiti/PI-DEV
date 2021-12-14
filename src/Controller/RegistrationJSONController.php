<?php

namespace App\Controller;

use Proxies\__CG__\App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RegistrationJSONController extends AbstractController
{
    /**
     * @Route("/registration/j/s/o/n", name="registration_j_s_o_n")
     */
    public function index(): Response
    {
        return $this->render('registration_json/index.html.twig', [
            'controller_name' => 'RegistrationJSONController',
        ]);
    }
    /**
     * @Route("/RegisterJSON/new", name="RegisterJSON")
     */
    public function RegisterJSON(Request  $request,NormalizerInterface $Normalizer,UserPasswordEncoderInterface $passwordEncoder,MailerInterface $mailer): Response
    {

        $password =$request->query->get("password");
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setEmail($request->get('email'));
        //$user->setRoles($request->get('roles'));

        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );
        $user->setCin($request->get('cin'));
        $user->setPhone($request->get('phone'));
        $user->setName($request->get('name'));
        $user->setGenre($request->get('genre'));
        /* $user->setSuspension($request->get('suspension'));
        $user->setRaisonsuspension($request->get('raisonsuspension')); */


        $entityManager->persist($user);
        $entityManager->flush();
        //email
        $email = (new Email())
            ->from('para.diseesprit@gmail.com')
            ->to((String)$user->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('[Paradise] Traitement d inscription !')
            //->text('Sending emails is fun again!')
            ->html('<p>Bonjour cher(e) Mr/Mme </p><br>
                   <p>Votre inscription  a été avec succees'.'</p><br>');
        $mailer->send($email);


        $json = $Normalizer ->normalize($user, 'json',['groups'=>'post:read']);
        //return $this->render('registration_json/reg_json.html.twig',['groups'=>$json]);
        return new Response(json_encode($json));
    }
}
