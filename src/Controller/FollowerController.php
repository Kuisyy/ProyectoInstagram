<?php

namespace App\Controller;

use App\Entity\Followers;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FollowerController extends AbstractController
{
    #[Route('/follower', name: 'app_follower')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $currentUser = $this->getUser();

        if ($request->isMethod('POST')) {
            if ($request->request->has('follow')) {
                $followedId = $request->request->get('follow');
                $userToFollow = $entityManager->getRepository(User::class)->find($followedId);
                
                if ($userToFollow && $userToFollow !== $currentUser) {
                    $follower = new Followers();
                    $follower->setFollower($currentUser);
                    $follower->setFollowed($userToFollow);
                    
                    $entityManager->persist($follower);
                    $entityManager->flush();
                }
            } elseif ($request->request->has('unfollow')) {
                $unfollowId = $request->request->get('unfollow');
                $follower = $entityManager->getRepository(Followers::class)->findOneBy([
                    'follower' => $currentUser,
                    'followed' => $unfollowId
                ]);
                
                if ($follower) {
                    $entityManager->remove($follower);
                    $entityManager->flush();
                }
            }
            
            return $this->redirectToRoute('app_follower');
        }

        return $this->render('follower/index.html.twig', [
            'users' => $users,
            'currentUser' => $currentUser
        ]);
    }
}
