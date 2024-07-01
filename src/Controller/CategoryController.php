<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/new', name: 'app_category')]
    #[Route('/admin/category/new', name: 'app_category')]
    public function index(Request $request, EntityManagerInterface $manager, CategoryRepository $categoryRepository): Response
    {
        if(!$this->getUser()){return $this->redirectToRoute("app_articles");}


        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute("app_category");

        }



        return $this->render('category/index.html.twig', [
            "form"=>$form->createView(),
            "categories"=>$categoryRepository->findAll(),
        ]);
    }

    #[Route('/category/delete/{id}', name: 'delete_category')]
    #[Route('/admin/category/delete/{id}', name: 'delete_category')]

    public function delete(Category $category, EntityManagerInterface $manager): Response
    {
        $manager->remove($category);
        $manager->flush();

        return $this->redirectToRoute('app_category');


    }

    #[Route('/category/edit/{id}', name: 'edit_category')]
    #[Route('/admin/category/edit/{id}', name: 'edit_category')]
    public function edit(Request $request, EntityManagerInterface $manager, Category $category):Response
    {
        $form = $this->createForm(CategoryType::class, $category);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute("app_category");
        }



        return $this->render('category/edit.html.twig', [
            "form"=>$form->createView(),
        ]);

    }
}
