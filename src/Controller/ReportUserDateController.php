<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use App\Form\ReportUserDateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportUserDateController extends AbstractController
{
    #[Route('/report/user/date', name: 'app_report_user_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $workTime = new WorkTime();
        $form = $this->createForm(ReportUserDateType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $visibleResult = true;
            $idUser = $workTime->getUser()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getUserDataWorkTimeSum($idUser, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getUserDataWorkTime($idUser, $dateStart, $dateEnd);
        }

        return $this->render('report_user_date/index.html.twig', [
            'controller_name' => 'Raport czasu pracy pracownika',
            'reportUserDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays
        ]);
    }
}
