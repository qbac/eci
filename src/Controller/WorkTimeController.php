<?php

namespace App\Controller;

use App\Entity\Employ;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Form\WorkTimeEditType;
use App\Form\WorkTimeType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class WorkTimeController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN',statusCode: 404, message: 'Nie masz dostępu do tej strony')]
    #[Route('/worktime', name: 'app_work_time')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
            $em = $doctrine->getManager();
            $workTime = new WorkTime();
            $form = $this->createForm(WorkTimeType::class, $workTime);
            $form->handleRequest($request);
            $defaultDate = new \DateTime('now - 1 day');
    
            if ($form->isSubmitted() && $form->isValid())
            {
                $nextAction = $form->get('add')->isClicked() ? 'new_edit': 'preview';
                //$em = $doctrine->getManager();
                if ($nextAction == 'new_edit'){
                    $us = $workTime->getUser()->getEmploy();
                    $costHour = $workTime->getUser()->getCostHour();
                    $proj = $workTime->getProject();
                    $find = $em->getRepository(WorkTime::class)->findOneBy([
                        'work_date' => $form->get('work_date')->getData(),
                        'project' => $proj,
                        'user' => $workTime->getUser()
                    ]);
                    $wd = $form->get('work_date')->getData()->format('Y-m-d');
                    //$time = $form->get('work_time')->getData()->format('H:i');
                    $workStart = new DateTime($form->get('work_start')->getData()->format('H:i'));
                    $workEnd = new DateTime($form->get('work_end')->getData()->format('H:i'));
                    $wt = $workEnd->diff($workStart);
                    //$wt->format('%H:%I:%S');
                    $date = new DateTime($wt->format('%H:%I:%S'));


                    if($find) {
                        $this->addFlash('warning', 'Wpis już istnieje. Został poprawiony.');
                        //$find->setWorkTime($form->get('work_time')->getData());
                        $find->setWorkTime($date);
                        $find->setCostHour($costHour);
                        $find->setWorkStart($form->get('work_start')->getData());
                        $find->setWorkEnd($form->get('work_end')->getData());
                        $find->setTravelTime($form->get('travel_time')->getData());
                        $em->flush();
                    } else {
                        $workTime->setEmploy($us);
                        $workTime->setCostHour($costHour);
                        $workTime->setWorkTime($date);
                        $em->persist($workTime);
                        $em->flush();
                        $flash = $wd.' '.$wt->format('%H:%I').', '.$workTime->getProject()->getName().', '.$workTime->getUser()->getFirstName().' '.$workTime->getUser()->getLastName();
                        $this->addFlash('success', 'Wpis został dodany. '.$flash);
                    }
                }
            }
            if ($form->get('work_date')->getData())
            {
                $defaultDate = $form->get('work_date')->getData();
            }
            
            $defaultDateData = $em->getRepository(WorkTime::class)->findBy(['work_date' => $defaultDate],['project' => 'ASC']);
    
            return $this->render('work_time/index.html.twig', [
                'controller_name' => 'WorkTimeController',
                'workTimeForm' => $form->createView(),
                'defaultData' => $defaultDateData
            ]);
    }
    #[IsGranted('ROLE_ADMIN',statusCode: 404, message: 'Nie masz dostępu do tej strony')]
    #[Route('/worktime/edit/{id}', name: 'app_work_time_edit')]
    public function workTimeEdit(WorkTime $workTime ,Request $request, EntityManagerInterface $em)
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $form = $this->createForm(WorkTimeEditType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workStart = new DateTime($form->get('work_start')->getData()->format('H:i'));
            $workEnd = new DateTime($form->get('work_end')->getData()->format('H:i'));
            $wt = $workEnd->diff($workStart);
                    //$wt->format('%H:%I:%S');
            $date = new DateTime($wt->format('%H:%I:%S'));
            $workTime->setWorkTime($date);
            $em->persist($workTime);
            $em->flush();
            $this->addFlash('success', 'Poprawiono Dane użytkownika');
            return $this->redirectToRoute('app_work_time');
        }
        
        return $this->render('work_time/edit.html.twig', [
            'controller_name' => 'Edytuj czas pracy pracownika',
            'editWorkTimeForm' => $form->createView()
        ]);
    }
}
