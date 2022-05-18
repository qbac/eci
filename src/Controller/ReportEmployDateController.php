<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use App\Form\ReportEmployDateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReportEmployDateController extends AbstractController
{
    #[Route('/report/employ/date', name: 'app_report_employ_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $resultTotal = array();
        $workTime = new WorkTime();
        $form = $this->createForm(ReportEmployDateType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $nextAction = $form->get('pdf')->isClicked() ? 'genPdf': 'preview';
            $visibleResult = true;
            $idEmploy = $workTime->getEmploy()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getEmployDataWorkTimeSum($idEmploy, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getEmployDataWorkTime($idEmploy, $dateStart, $dateEnd);
            $resultTotal = $workTimeRepository->getEmployDataTotalSum($idEmploy, $dateStart, $dateEnd);

            if ($nextAction == 'genPdf') {
                $employName = $workTime->getEmploy()->getName();
                return $this->pdfReport($idEmploy, $dateStart, $dateEnd, $employName, $workTimeRepository);
            }
        }

        return $this->render('report_employ_date/index.html.twig', [
            'controller_name' => 'ReportEmployDateController',
            'reportEmployDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal
        ]);
    }

    #[Route('/report/employ/pdf', name: 'app_report_employ_pdf')]
    public function pdfReport($idEmploy, $dateStart, $dateEnd, $employName, WorkTimeRepository $workTimeRepository)
    {
        $resultSum = $workTimeRepository->getEmployDataWorkTimeSum($idEmploy, $dateStart, $dateEnd);
        $resultDays = $workTimeRepository->getEmployDataWorkTime($idEmploy, $dateStart, $dateEnd);
        $resultTotal = $workTimeRepository->getEmployDataTotalSum($idEmploy, $dateStart, $dateEnd);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');
        $pdfOptions->set('isPhpEnabled', true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('report_employ_date/pdf.html.twig', [
            'title' => "Raport",
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'employName' => $employName,
        ]);
        
        $filename = 'raport-emp_'.$employName.'_'.$dateStart.'_'.$dateEnd.'.pdf';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, [
            "Attachment" => true
        ]);

        //return $this->redirectToRoute('app_report_user_date');
    }
}
