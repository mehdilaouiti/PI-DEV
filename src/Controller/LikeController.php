<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Like;
use App\Repository\LikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LikeController extends AbstractController
{
    /**
     * @Route("/like", name="like")
     */
    public function index(): Response
    {
        return $this->render('like/index.html.twig', [
            'controller_name' => 'LikeController',
        ]);
    }

    /**
     * @Route("/article/{id}/like", name="art_like")
     * @param Article $article
     * @param LikeRepository $likeRepository
     */
    public function like(Article $article,LikeRepository $likeRepository,UserRepository $repo ):Response{
        $user=$this->getUser();
        //$user=$this->get('security.token_storage')->getToken()->getUser();
        //dd($user);
        if(!$user) return $this->json([
            'code' =>403,
            'message' =>"Unauthorized"
        ],403);
        if($article->isLikedByUser($user)){
            $like=$likeRepository->findOneBy([
                'art' =>$article,
                'user' =>$user
            ]);
            $em = $this->getDoctrine()->getManager();
            $em->remove($like);
            $em->flush();
            return $this->json([
                'code'=>200,
                'message'=>'Like bien supprimé',
                'likes'=>$likeRepository->count(['article'=>$article])
            ],200);
        }
        $like=new Like();
        $like->setArt($article)
            ->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($like);
        $em->flush();

        return $this->json(['code'=>200,
            'message'=>'like bien ajouté',
            'likes'=>$likeRepository->count(['article'=>$article])],200);


    }
}
