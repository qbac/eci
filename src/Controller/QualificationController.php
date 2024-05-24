<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QualificationController extends AbstractController
{
    #[Route('/qualification', name: 'app_qualification')]
    public function index(): Response
    {
        return $this->render('qualification/index.html.twig', [
            'controller_name' => 'QualificationController',
        ]);
    }
}
