<?php

namespace App\Controller;

use App\Entity\Publicacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publicacionesSeguidas = [];
        if ($this->getUser()) {
            $publicacionesSeguidas = $entityManager->getRepository(Publicacion::class)
                ->findPublicacionesFromFollowed($this->getUser());
        }

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'publicacionesSeguidas' => $publicacionesSeguidas,
        ]);
    }
}
