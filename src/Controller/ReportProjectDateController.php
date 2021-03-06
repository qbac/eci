<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Form\ReportProjectDateType;
use App\Repository\WorkTimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
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
        $resultTotal = array();
        $form = $this->createForm(ReportProjectDateType::class, $workTime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $nextAction = $form->get('pdf')->isClicked() ? 'genPdf': 'preview';
            $visibleResult = true;
            $idProject = $workTime->getProject()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getProjectDataWorkTimeSum($idProject, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getProjectDataWorkTime($idProject, $dateStart, $dateEnd);
            $resultTotal = $workTimeRepository->getProjectDataTotalSum($idProject, $dateStart, $dateEnd);

            if ($nextAction == 'genPdf') {
                $projectName = $workTime->getProject()->getName();
                return $this->pdfReport($idProject, $dateStart, $dateEnd, $projectName, $workTimeRepository);
            }
        }

        return $this->render('report_project_date/index.html.twig', [
            'controller_name' => 'ReportProjectDateController',
            'reportProjectDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal
        ]);
    }

    #[Route('/report/project/pdf', name: 'app_report_project_pdf')]
    public function pdfReport($idProject, $dateStart, $dateEnd, $projectName, WorkTimeRepository $workTimeRepository)
    {
        $resultSum = $workTimeRepository->getProjectDataWorkTimeSum($idProject, $dateStart, $dateEnd);
        $resultDays = $workTimeRepository->getProjectDataWorkTime($idProject, $dateStart, $dateEnd);
        $resultTotal = $workTimeRepository->getProjectDataTotalSum($idProject, $dateStart, $dateEnd);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');
        $pdfOptions->set('isPhpEnabled', true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('report_project_date/pdf.html.twig', [
            'title' => "Raport czasu pracy",
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'projectName' => $projectName,
        ]);
        
        $filename = 'raport-projekt_'.$projectName.'_'.$dateStart.'_'.$dateEnd.'.pdf';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, [
            "Attachment" => true
        ]);

        //return $this->redirectToRoute('app_report_user_date');
    }
}
