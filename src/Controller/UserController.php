<?php

namespace App\Controller;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $listUsers = $em->getRepository(User::class)->findBy(['active' => '1']);
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'listUsers' => $listUsers
        ]);
    }
}
