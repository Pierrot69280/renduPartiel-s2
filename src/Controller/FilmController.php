<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Place;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    #[Route('/', name: 'app_films')]
    public function index(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findAll();
        return $this->render('film/index.html.twig', [
            'films' => $films
        ]);
    }

    #[Route('/film/{id}', name: 'app_show')]
    public function show(Film $film): Response
    {
        return $this->render('film/show.html.twig', [
            'film' => $film,
        ]);
    }

    #[Route('/create', name: 'app_create')]
    #[Route('/admin/create', name: 'app_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("app_films");
        }

        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $film->setAuthor($this->getUser());
            $manager->persist($film);
            $manager->flush();

            // Créer 45 places pour le film
            for ($i = 1; $i <= 45; $i++) {
                $place = new Place();
                $place->setNumber($i);
                $place->setFilm($film);
                $manager->persist($place);
            }
            $manager->flush();

            return $this->redirectToRoute('app_films', ["id" => $film->getId()]);
        }

        return $this->render('film/create.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: 'app_delete')]
    #[Route('/admin/delete/{id}', name: 'app_delete')]
    public function delete(Film $film, EntityManagerInterface $manager): Response
    {
        $manager->remove($film);
        $manager->flush();
        return $this->redirectToRoute("app_films");
    }

    #[Route('/edit/{id}', name: 'app_edit')]
    #[Route('/admin/edit/{id}', name: 'app_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Film $film): Response
    {
        $formulaire = $this->createForm(FilmType::class, $film);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $manager->flush();

            return $this->redirectToRoute('app_films');
        }

        return $this->render('film/edit.html.twig', [
            'formulaire' => $formulaire->createView()
        ]);
    }

    #[Route('/film/{id}/reserve', name: 'app_reserve')]
    public function reserve(Film $film, PlaceRepository $placeRepository, EntityManagerInterface $manager, Request $request): Response
    {
        $places = $placeRepository->findBy(['film' => $film]);

        if ($request->isMethod('POST')) {
            $placeId = $request->request->get('place_id');
            $place = $placeRepository->find($placeId);

            if ($place) {
                // Vérifier si la place est déjà réservée par l'utilisateur connecté
                if ($place->isReservedByUser($this->getUser())) {
                    $place->setIsReserved(false);
                    $place->setUser(null);
                    $manager->flush();
                } elseif (!$place->getIsReserved()) {
                    $place->setIsReserved(true);
                    $place->setUser($this->getUser());
                    $manager->flush();
                }

                return $this->redirectToRoute('app_films');
            }
        }

        return $this->render('film/reserve.html.twig', [
            'film' => $film,
            'places' => $places,
        ]);
    }
}
