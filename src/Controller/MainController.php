<?php

namespace App\Controller;

use App\Entity\Publicacion;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/main/search', name: 'app_main_search', methods: ['GET', 'POST'])]
    public function search(EntityManagerInterface $entityManager, Request $request): Response
    {
        if($request->isMethod('POST')){
            $searchTerm = $request->request->get('username');
            
            // Almacenar el término de búsqueda en la sesión
            $request->getSession()->set('search_term', $searchTerm);
            
            return $this->redirectToRoute('app_main_search');
        }
        
        // Recuperar el término de búsqueda de la sesión
        $searchTerm = $request->getSession()->get('search_term');
        
        if ($searchTerm) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $searchTerm]);
            
            // Buscar publicaciones que contengan el término en la descripción
            $publicaciones = $entityManager->getRepository(Publicacion::class)
                ->createQueryBuilder('p')
                ->where('p.description LIKE :term')
                ->setParameter('term', '%'.$searchTerm.'%')
                ->getQuery()
                ->getResult();
            
            // Limpiar la sesión
            $request->getSession()->remove('search_term');
            
            return $this->render('main/search.html.twig', [
                'user' => $user,
                'publicaciones' => $publicaciones,
                'searchTerm' => $searchTerm
            ]);
        }
        
        return $this->redirectToRoute('app_main');
    }
    
}
