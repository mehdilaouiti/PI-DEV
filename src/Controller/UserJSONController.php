<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserJSONController extends AbstractController
{
    /**
     * @Route("/user/j/s/o/n", name="user_j_s_o_n")
     */
    public function index(): Response
    {
        return $this->render('user_json/index.html.twig', [
            'controller_name' => 'UserJSONController',
        ]);
    }

    /**
     * @Route("/UserJSON/{id}", name="UserJSON")
     */
    public function UserJSONbyID(Request $request, $id, NormalizerInterface $Normalizer): Response
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $json = $Normalizer ->normalize($user, 'json',['groups'=>'post:read']);

        return new Response(json_encode($json));

    }

    /**
     * @Route("/AllUser", name="AllUser")
     */
    public function AllUser(NormalizerInterface $Normalizer): Response
    {

        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findAll();
        $json = $Normalizer ->normalize($user, 'json',['groups'=>'post:read']);

        return new Response(json_encode($json));

    }



    /**
     * @Route("/UpdateUserJSON/{id}", name="UpdateUserJSONJSON")
     */
    public function UpdateUserJSON(Request $request, NormalizerInterface $Normalizer,UserPasswordEncoderInterface $passwordEncoder, $id): Response
    {

        $user=  $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        $password =$request->query->get("password");

        $user->setEmail($request->get('email'));
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


        $this->getDoctrine()->getManager()->flush();

        $json = $Normalizer ->normalize($user, 'json',['groups'=>'post:read']);

        return new Response("mise a jour avec succes".json_encode($json));
    }
    /**
     * @Route ("/getPasswordByEmail", name="app_password")
     */

    public function getPasswordByEmail(Request $request){
        $email =$request->get ('email');
        $user =$this->getDoctrine()->getManager()->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user){

            $password =$user->getPassword();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($password);
            return new  JsonResponse($formatted);
        }
        return new Response ("User not found");
    }

    /**
     * @Route("/deleteUserJSON/{id}", name="DeleteUserJSON")
     */
    public function DeleteUserJSON(Request $request, NormalizerInterface $Normalizer, $id): Response
    {

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $json = $Normalizer ->normalize($user, 'json',['groups'=>'post:read']);

        return new Response(json_encode($json));
    }
}
