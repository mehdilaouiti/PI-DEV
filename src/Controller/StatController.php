<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;



class StatController extends AbstractController
{

    /**
     * @Route("/admin/stat", name="stat")
     */
    public function index(UserRepository $userRepo): Response
    {

        $userMen = $userRepo->findBy(array('genre'=>'Homme'));
        $userWomen = $userRepo->findBy(array('genre'=>'Femme'));
        $userMen=count($userMen);
        $userWomen=count($userWomen);

        return $this->render('stat/index.html.twig', [
            'userMen'=>$userMen,
            'userWomen'=>$userWomen,

        ]);
    }
}
