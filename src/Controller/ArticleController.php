<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_articles')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('article/index.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/article/{id}', name: 'app_show')]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


    #[Route('/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $manager):Response
    {

//        if(!$this->getUser()){return $this->redirectToRoute("app_articles");}
        $article = new Article();
        $form =  $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $twoote->setCreatedAt(new \DateTime());
//            $twoote->setAuthor($this->getUser());
            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('app_articles', ["id" => $article->getId()]);
        }

        return $this->render('article/create.html.twig',[
            'formulaire'=>$form->createView()
        ]);
    }

    #[Route('/delete/{id}', name:'app_delete')]
    public function delete(Article $article, EntityManagerInterface $manager):Response
    {

        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute("app_articles");

    }

    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Article $article): Response
    {
        $formulaire = $this->createForm(ArticleType::class, $article);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_articles');
        }

        return $this->render('article/edit.html.twig', [
            'formulaire' => $formulaire->createView()
        ]);
    }
}
