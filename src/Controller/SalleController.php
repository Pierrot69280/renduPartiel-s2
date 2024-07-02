<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SalleController extends AbstractController
{
    #[Route('/salle/new', name: 'app_salle')]
    #[Route('/admin/salle/new', name: 'app_salle')]
    public function index(Request $request, EntityManagerInterface $manager, SalleRepository $salleRepository): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute("app_films");}


        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($salle);
            $manager->flush();

            return $this->redirectToRoute("app_salle");

        }



        return $this->render('salle/index.html.twig', [
            "form"=>$form->createView(),
            "salles"=>$salleRepository->findAll(),
        ]);
    }

    #[Route('/salle/delete/{id}', name: 'delete_salle')]
    #[Route('/admin/salle/delete/{id}', name: 'delete_salle')]

    public function delete(Salle $salle, EntityManagerInterface $manager): Response
    {
        $manager->remove($salle);
        $manager->flush();

        return $this->redirectToRoute('app_salle');


    }

    #[Route('/salle/edit/{id}', name: 'edit_salle')]
    #[Route('/admin/salle/edit/{id}', name: 'edit_salle')]
    public function edit(Request $request, EntityManagerInterface $manager, Salle $salle):Response
    {
        $form = $this->createForm(SalleType::class, $salle);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($salle);
            $manager->flush();

            return $this->redirectToRoute("app_salle");
        }



        return $this->render('salle/edit.html.twig', [
            "form"=>$form->createView(),
        ]);

    }
}
