<?php

namespace App\Controller;

use App\Entity\Fabricant;
use App\Form\FabricantType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FabricantController extends AbstractController
{
    /**
     * @Route("/fabricant", name="fabricant")
     */
    public function index(): Response
    {
        return $this->render('fabricant/index.html.twig', [
            'controller_name' => 'FabricantController',
        ]);
    }

    /**
     * @Route("/admin/fabricant", name="readfabadmin")
     */
    public function readFabAdmin(): Response
    {
        $repo=$this->getDoctrine()->getRepository(Fabricant::class);
        $listfab =$repo->findAll();

        return $this->render('admin/fabricant/read.html.twig', [
            'fabricant' => $listfab]);
    }
    /**
     * @Route("/admin/ajouter_fabricant", name="createfabadmin" ,methods={"GET","POST"})
     */

    public function createFabAdmin (Request $req){
        $fabricant=new Fabricant();
        $form=$this->createForm(FabricantType::class,$fabricant);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($fabricant);
            $em->flush();
            return $this->redirectToRoute('readfabadmin');
        }
        return $this->render('admin/fabricant/create.html.twig', [
            'form' => $form->createView()]);

    }
    /**
     * @Route("/admin/update_fabricant/{id}", name="updatefabadmin")
     */

    public function updatefabAdmin(Request $req,$id){
        $repo=$this->getDoctrine()->getRepository(Fabricant::class);
        $fabricant=$repo->find($id);
        $form=$this->createForm(FabricantType::class,$fabricant);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('readfabadmin');
        }
        return $this->render('/admin/fabricant/update.html.twig', [
            'form' => $form->createView()]);
    }
    /**
     * @Route("/admin/delete_fabricant/{id}", name="deletefabadmin")
     */

    public function deleteCatAdmin ($id){
        $em = $this->getDoctrine()->getManager();
        $fabricant=$em->getRepository(Fabricant::class)->find($id);
        $em->remove($fabricant);
        $em->flush();
        return $this->redirectToRoute('readfabadmin');
    }
}
