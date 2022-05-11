<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use App\Form\ReportEmployDateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportEmployDateController extends AbstractController
{
    #[Route('/report/employ/date', name: 'app_report_employ_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $workTime = new WorkTime();
        $form = $this->createForm(ReportEmployDateType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $visibleResult = true;
            $idEmploy = $workTime->getEmploy()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getEmployDataWorkTimeSum($idEmploy, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getEmployDataWorkTime($idEmploy, $dateStart, $dateEnd);
        }

        return $this->render('report_employ_date/index.html.twig', [
            'controller_name' => 'ReportEmployDateController',
            'reportEmployDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays
        ]);
    }
}
