<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Form\ReportProjectDateType;
use App\Repository\WorkTimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportProjectDateController extends AbstractController
{
    #[Route('/report/project/date', name: 'app_report_project_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $workTime = new WorkTime();
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $form = $this->createForm(ReportProjectDateType::class, $workTime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $visibleResult = true;
            $idProject = $workTime->getProject()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getProjectDataWorkTimeSum($idProject, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getProjectDataWorkTime($idProject, $dateStart, $dateEnd);
        }

        return $this->render('report_project_date/index.html.twig', [
            'controller_name' => 'ReportProjectDateController',
            'reportProjectDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays
        ]);
    }
}
