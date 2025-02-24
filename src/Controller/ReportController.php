<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\Publicacion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReportController extends AbstractController
{
    #[Route('/publicacion/{id}/report', name: 'app_report_post')]
    public function reportPost(Publicacion $publicacion, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $report = new Report();
            $report->setPublicacion($publicacion);
            $report->setReporter($this->getUser());
            $report->setReason($request->request->get('reason'));
            $report->setResolved(false);

            $em->persist($report);
            $em->flush();

            $this->addFlash('success', 'La publicación ha sido reportada');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('report/new.html.twig', [
            'publicacion' => $publicacion
        ]);
    }

    #[Route('/admin/report/{id}/resolve', name: 'app_report_resolve')]
    public function resolveReport(Report $report, EntityManagerInterface $em): Response
    {
        $report->setResolved(true);
        $em->flush();

        return $this->redirectToRoute('app_admin_dashboard');
    }
} 