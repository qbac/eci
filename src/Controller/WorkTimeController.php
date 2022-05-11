<?php

namespace App\Controller;

use App\Entity\Employ;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Form\WorkTimeType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class WorkTimeController extends AbstractController
{
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
                    $time = $form->get('work_time')->getData()->format('H:i');
                    
                    if($find) {
                        $this->addFlash('warning', 'Wpis już istnieje. Został poprawiony.');
                        $find->setWorkTime($form->get('work_time')->getData());
                        $find->setCostHour($costHour);
                        $em->flush();
                    } else {
                        $workTime->setEmploy($us);
                        $workTime->setCostHour($costHour);
                        $em->persist($workTime);
                        $em->flush();
                        $flash = $wd.' '.$time.', '.$workTime->getProject()->getName().', '.$workTime->getUser()->getFirstName().' '.$workTime->getUser()->getLastName();
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
}
