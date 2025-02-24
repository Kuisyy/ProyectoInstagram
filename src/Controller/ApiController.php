<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\User;
use App\Entity\Like;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/posts/recent', name: 'api_recent_posts', methods: ['GET'])]
    public function getRecentPosts(EntityManagerInterface $em): JsonResponse
    {
        $posts = $em->getRepository(Publicacion::class)
            ->findBy(['userPost' => $this->getUser()], ['id' => 'DESC'], 10);
            
        return $this->json($posts, 200, [], ['groups' => 'post']);
    }

    #[Route('/users/{username}', name: 'api_user_info', methods: ['GET'])]
    public function getUserInfo(string $username, EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->findOneBy(['username' => $username]);
        
        if (!$user) {
            return $this->json(['error' => 'Usuario no encontrado'], 404);
        }
        
        return $this->json($user, 200, [], ['groups' => 'user']);
    }

    #[Route('/posts', name: 'api_create_post', methods: ['POST'])]
    public function createPost(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $post = new Publicacion();
        $post->setUserPost($this->getUser());
        $post->setDescription($data['description']);
        // Manejo de imagen...
        
        $em->persist($post);
        $em->flush();
        
        return $this->json($post, 201);
    }

    #[Route('/posts/{id}/like', name: 'api_toggle_like', methods: ['POST'])]
    public function toggleLike(Publicacion $post, EntityManagerInterface $em): JsonResponse
    {
        $like = $em->getRepository(Like::class)->findOneBy([
            'publicacion' => $post,
            'user' => $this->getUser()
        ]);

        if ($like) {
            $em->remove($like);
            $post->setLikes($post->getLikes() - 1);
        } else {
            $like = new Like();
            $like->setPublicacion($post);
            $like->setUser($this->getUser());
            $em->persist($like);
            $post->setLikes($post->getLikes() + 1);
        }

        $em->flush();
        return $this->json(['likes' => $post->getLikes()]);
    }
} 