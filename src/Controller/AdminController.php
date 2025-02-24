<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        // Estadísticas básicas
        $stats = [
            'topPosts' => $em->getRepository(Publicacion::class)->findTopLikedPosts(),
            'topCommented' => $em->getRepository(Publicacion::class)->findTopCommentedPosts(),
            'topUsers' => $em->getRepository(User::class)->findTopFollowedUsers(),
            'totalPosts' => $em->getRepository(Publicacion::class)->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'reports' => $em->getRepository(Report::class)->findBy(['resolved' => false])
        ]);
    }
} 