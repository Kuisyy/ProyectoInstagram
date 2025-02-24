<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\Comments;
use App\Entity\Like;
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
            $publicacion->setLikes(0);
            $publicacion->setIsVisible(1);
            
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

    #[Route('/publicacion/{id}/like', name: 'app_publicacion_like')]
    public function toggleLike(Publicacion $publicacion, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $like = $entityManager->getRepository(Like::class)->findOneBy([
            'publicacion' => $publicacion,
            'user' => $user
        ]);

        if ($like) {
            $entityManager->remove($like);
            $publicacion->setLikes($publicacion->getLikes() - 1);
        } else {
            $like = new Like();
            $like->setPublicacion($publicacion);
            $like->setUser($user);
            $entityManager->persist($like);
            $publicacion->setLikes($publicacion->getLikes() + 1);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }

    #[Route('/publicacion/show/{id}', name: 'app_publicacion_show')]
    public function show(Publicacion $publicacion, EntityManagerInterface $entityManager): Response
    {
        $likes = $entityManager->getRepository(Like::class)->findBy(['publicacion' => $publicacion]);
        $comments = $entityManager->getRepository(Comments::class)->findBy(['publicacion' => $publicacion]);
        return $this->render('publicacion/show.html.twig', [
            'publicacion' => $publicacion,
            'likes' => $likes,
            'comments' => $comments
        ]);
    }

}
