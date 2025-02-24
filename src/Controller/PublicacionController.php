<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\Comments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PublicacionController extends AbstractController
{
    #[Route('/publicacion', name: 'app_publicacion_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $publicaciones = $entityManager->getRepository(Publicacion::class)->findAll();
        $comments = $entityManager->getRepository(Comments::class)->findAll();
        
        return $this->render('publicacion/index.html.twig', [
            'publicaciones' => $publicaciones,
            'comments' => $comments,
        ]);
    }


    #[Route('/publicacion/new', name: 'app_publicacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($request->isMethod('POST')) {
            $publicacion = new Publicacion();
            $publicacion->setUserPost($this->getUser());
            $publicacion->setDescription($request->request->get('descripcion'));

            $file = $request->files->get('img');
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $publicacion->setImg($newFilename);
                } catch (\Exception $e) {
                    return $this->redirectToRoute('app_publicacion_new');
                }
            }

            $entityManager->persist($publicacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_publicacion_index');
        }

        return $this->render('publicacion/new.html.twig');
    }
    
}
