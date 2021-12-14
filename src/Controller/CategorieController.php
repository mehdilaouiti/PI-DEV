<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    /**
     * @Route("/cate", name="categorie")
     */
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    /**
     * @Route("/admin/categorie", name="readcatadmin")
     */
    public function readCatAdmin(): Response
    {
        $repo=$this->getDoctrine()->getRepository(Categorie::class);
        $listcat =$repo->findAll();

        return $this->render('admin/categorie/read.html.twig', [
            'categorie' => $listcat]);
    }
    /**
     * @Route("/admin/ajouter_categorie", name="createcatadmin" ,methods={"GET","POST"})
     */

    public function createCatAdmin (Request $req){
        $categorie=new Categorie();
        $form=$this->createForm(CategorieType::class,$categorie);
        $form->add('Ajouter',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('readcatadmin');
        }
        return $this->render('admin/categorie/create.html.twig', [
            'form' => $form->createView()]);

    }
    /**
     * @Route("/admin/update_categorie/{id}", name="updatecatadmin")
     */

    public function updatecatAdmin(Request $req,$id){
        $repo=$this->getDoctrine()->getRepository(Categorie::class);
        $categorie=$repo->find($id);
        $form=$this->createForm(CategorieType::class,$categorie);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('readcatadmin');
        }
        return $this->render('/admin/categorie/update.html.twig', [
            'form' => $form->createView()]);
    }
    /**
     * @Route("/admin/delete_categorie/{id}", name="deletecatadmin")
     */

    public function deleteCatAdmin ($id){
        $em = $this->getDoctrine()->getManager();
        $categorie=$em->getRepository(Categorie::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('readcatadmin');
    }
}
