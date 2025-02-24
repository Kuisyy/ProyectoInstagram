<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Publicacion;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $comments = $entityManager->getRepository(Comments::class)->findAll();
        return $this->render('comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/comment/new/{id}', name: 'app_comment_new')]
    public function new(Request $request, Publicacion $publicacion, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $comment = new Comments();
            $comment->setComment($request->request->get('comment'));
            $comment->setUser($this->getUser());
            $comment->setPublicacion($publicacion);
            $comment->setIsVisible(1);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_publicacion_index');
        }

        return $this->render('comment/new.html.twig', [
            'publicacion' => $publicacion,
        ]);
    }

    #[Route('/comment/hide/{id}', name: 'app_comment_hide')]
    public function hide(int $id, CommentsRepository $commentsRepository, EntityManagerInterface $entityManager): Response
    {

        $comment = $commentsRepository -> find($id);

        if($comment -> isVisible()){
            $comment -> setIsVisible(0);
        }else{
            $comment -> setIsVisible(1);
        }

        $entityManager->persist($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_publicacion_index');
    }
}
