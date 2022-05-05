<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportProjectDateController extends AbstractController
{
    #[Route('/report/project/date', name: 'app_report_project_date')]
    public function index(): Response
    {
        return $this->render('report_project_date/index.html.twig', [
            'controller_name' => 'ReportProjectDateController',
        ]);
    }
}
