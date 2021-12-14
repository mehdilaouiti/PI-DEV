<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginJSONController extends AbstractController
{
    /**
     * @Route("/login/j/s/o/n", name="login_j_s_o_n")
     */
    public function index(): Response
    {
        return $this->render('login_json/index.html.twig', [
            'controller_name' => 'LoginJSONController',
        ]);
    }

    /**
     * @Route("/serviceLogin/{email}/{password}", name="serviceLogin")
     */
    public function serviceLogin(Request $request,$email,$password , UserPasswordEncoderInterface $passwordEncoder, NormalizerInterface $Normalizer,UserProviderInterface $userProvider)
    {
        $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user==null) {
            return new JsonResponse("verify champs", 400);

        }

        if ($user) {
            if ($passwordEncoder->isPasswordValid($user, $password)) {
                //$data = $Normalizer->normalize($user, 'json', ["requette" => ['reponse' => 'yes', 'user' => ['email' => $user->getEmail(), 'roles' => $user->getRoles(), 'password' => $user->getPassword(), 'name' => $user->getName(), 'cin' => $user->getCin(), 'id_user' => $user->getId(), 'phone' => $user->getPhone(),
                //  'genre' => $user->getGenre()]]]);
                $data = $Normalizer->normalize($user);

            } else {
                return new JsonResponse("email or password incorrect", 400);
            }

        }
        $pass=password_hash($password, PASSWORD_DEFAULT);

        header('Content-type: application/json');
        return  new Response(json_encode( $data ));
    }
}
