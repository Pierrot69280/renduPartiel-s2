<?php

namespace App\Controller;

use App\Entity\Image;

use App\Form\ImageType;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('/image/add', name: 'add_image')]
    public function index(Request $request, EntityManagerInterface $manager, ImageRepository $imageRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('app_product');
        }

        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);
        if($formImage->isSubmitted() && $formImage->isValid())
        {
            $manager->persist($image);
            $manager->flush();
            return $this->redirectToRoute("app_home");
        }
        return $this->render("image/selection.html.twig", ['formImage' => $formImage->createView(), "images"=>$imageRepository->findAll()]);
    }
}
