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
use Doctrine\Persistence\ManagerRegistry;
class ReportProjectDateController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/report/project/date', name: 'app_report_project_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $workTime = new WorkTime();
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $resultTotal = array();
        $monthWork = array();
        $monthsWork = array();
        $form = $this->createForm(ReportProjectDateType::class, $workTime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $nextAction = $form->get('pdf')->isClicked() ? 'genPdf': 'preview';
            $nextAction2 = $form->get('pdfDetails')->isClicked() ? 'genPdf': 'preview';
            $visibleResult = true;
            $idProject = $workTime->getProject()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getProjectDataWorkTimeSum($idProject, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getProjectDataWorkTime($idProject, $dateStart, $dateEnd);
            $resultTotal = $workTimeRepository->getProjectDataTotalSum($idProject, $dateStart, $dateEnd);

            if ($nextAction == 'genPdf') {
                $projectName = $workTime->getProject()->getName();
                return $this->pdfReport($idProject, $dateStart, $dateEnd, $projectName, $workTimeRepository, 1);
            }

            if ($nextAction2 == 'genPdf') {
                $projectName = $workTime->getProject()->getName();
                return $this->pdfReport($idProject, $dateStart, $dateEnd, $projectName, $workTimeRepository, 2);
            }
            
            $numMonths = $workTimeRepository->getNumberMonthsTwoDates($dateStart, $dateEnd);
            //var_dump($numMonths);
            $i = 0;
            foreach ($numMonths as $numMonth)
            {
                $monthsWork[$i] = $workTimeRepository->getProjectMonth($idProject, $numMonth['month'], $numMonth['year']);
                $i++;
            }
            $monthWork = $workTimeRepository->getProjectMonth($idProject, $form->get('work_date_start')->getData()->format('m'), $form->get('work_date_start')->getData()->format('Y'));
        }

        return $this->render('report_project_date/index.html.twig', [
            'controller_name' => 'ReportProjectDateController',
            'reportProjectDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal,
            'monthView' => $monthsWork
        ]);
    }

    // public function reportMonth($month, $year, $idProject)
    // {
    //     $em = $this->doctrine->getManager();
    //     $users = $em->getRepository(WorkTime::class)->getProjectUserMonthWork($idProject, $month, $year);
    //     //$users = $workTimeRepository->getProjectUserMonthWork($idProject, $month, $year);
    //     return $users;
    // }

    #[Route('/report/project/pdf', name: 'app_report_project_pdf')]
    public function pdfReport($idProject, $dateStart, $dateEnd, $projectName, WorkTimeRepository $workTimeRepository, $options)
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
        if ($options == 1) {
            $html = $this->renderView('report_project_date/pdf.html.twig', [
                'title' => "Raport czasu pracy",
                'resultReportSum' => $resultSum,
                'resultReportDays' => $resultDays,
                'resultTotal' => $resultTotal,
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd,
                'projectName' => $projectName,
            ]);
        }

        if ($options == 2) {
            $html = $this->renderView('report_project_date/pdfDetails.html.twig', [
                'title' => "Raport czasu pracy",
                'resultReportSum' => $resultSum,
                'resultReportDays' => $resultDays,
                'resultTotal' => $resultTotal,
                'dateStart' => $dateStart,
                'dateEnd' => $dateEnd,
                'projectName' => $projectName,
            ]);
        }

        $filename = 'raport-projekt_'.$projectName.'_'.$dateStart.'_'.$dateEnd.'.pdf';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, [
            "Attachment" => true
        ]);

        //return $this->redirectToRoute('app_report_user_date');
    }
//API

    //Total working time and costs
    #[Route('/api/project/sum/{idProject}', name: 'api_project_sum', methods:'GET')]
    public function getSumProject($idProject, WorkTimeRepository $workTimeRepository): Response
    {
        $dateStart = '2020-01-01';
        $dateEnd = date("Y-m-d");
        $resultTotal = $workTimeRepository->getProjectDataTotalSum($idProject, $dateStart, $dateEnd);
        return $this->json($resultTotal);
    }

    //time of work on the project of individual people
    #[Route('/api/project/worktimesum/{idProject}', name: 'api_project_work_time_sum', methods:'GET')]
    public function getWorkTimeSumProject($idProject, WorkTimeRepository $workTimeRepository): Response
    {
        $dateStart = '2020-01-01';
        $dateEnd = date("Y-m-d");
        $resultTotal = $workTimeRepository->getProjectDataWorkTimeSum($idProject, $dateStart, $dateEnd);
        return $this->json($resultTotal);
    }
}
