<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Repository\WorkTimeRepository;
use App\Form\ReportUserDateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Dompdf\Dompdf;
use Dompdf\Options;
class ReportUserDateController extends AbstractController
{
    #[Route('/report/user/date', name: 'app_report_user_date')]
    public function index(Request $request, WorkTimeRepository $workTimeRepository): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $resultSum = array();
        $visibleResult = false;
        $resultDays = array();
        $resultTotal = array();
        $workTime = new WorkTime();
        $form = $this->createForm(ReportUserDateType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $nextAction = $form->get('pdf')->isClicked() ? 'genPdf': 'preview';

            $visibleResult = true;
            $idUser = $workTime->getUser()->getId();
            $dateStart = $form->get('work_date_start')->getData()->format('Y-m-d');
            $dateEnd = $form->get('work_date_end')->getData()->format('Y-m-d');
            $resultSum = $workTimeRepository->getUserDataWorkTimeSum($idUser, $dateStart, $dateEnd);
            $resultDays = $workTimeRepository->getUserDataWorkTime($idUser, $dateStart, $dateEnd);
            $resultTotal = $workTimeRepository->getUserDataTotalSum($idUser, $dateStart, $dateEnd);
            
            if ($nextAction == 'genPdf') {
                $firstName = $workTime->getUser()->getFirstName();
                $lastName = $workTime->getUser()->getLastName();
                return $this->pdfReport($idUser, $dateStart, $dateEnd, $firstName, $lastName, $workTimeRepository);
            }
        }

        return $this->render('report_user_date/index.html.twig', [
            'controller_name' => 'Raport czasu pracy pracownika',
            'reportUserDateForm' => $form->createView(),
            'visibleResult' => $visibleResult,
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal
        ]);
    }
    #[Route('/report/user/pdf', name: 'app_report_user_pdf')]
    public function pdfReport($idUser, $dateStart, $dateEnd, $firstName, $lastName, WorkTimeRepository $workTimeRepository)
    {
        $resultSum = $workTimeRepository->getUserDataWorkTimeSum($idUser, $dateStart, $dateEnd);
        $resultDays = $workTimeRepository->getUserDataWorkTime($idUser, $dateStart, $dateEnd);
        $resultTotal = $workTimeRepository->getUserDataTotalSum($idUser, $dateStart, $dateEnd);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Courier');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('report_user_date/pdf.html.twig', [
            'title' => "Raport czasu pracy",
            'resultReportSum' => $resultSum,
            'resultReportDays' => $resultDays,
            'resultTotal' => $resultTotal,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'firstName' => $firstName,
            'lastName' => $lastName
        ]);
        
        $filename = 'raport-czasu-pracy_'.$firstName.'-'.$lastName.'_'.$dateStart.'_'.$dateEnd.'.pdf';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, [
            "Attachment" => true
        ]);

        //return $this->redirectToRoute('app_report_user_date');
    }
}
