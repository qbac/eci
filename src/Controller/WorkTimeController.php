<?php

namespace App\Controller;

use App\Entity\Employ;
use App\Entity\User;
use App\Entity\WorkTime;
use App\Form\WorkTimeType;
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
        $em = $doctrine->getManager();
        $workTime = new WorkTime();
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);
        $defaultDate = new \DateTime('now - 1 day');

        if ($form->isSubmitted() && $form->isValid())
        {
            //$em = $doctrine->getManager();
            $us = $workTime->getUser()->getEmploy();
            $proj = $workTime->getProject();
            $find = $em->getRepository(WorkTime::class)->findOneBy([
                'work_date' => $form->get('work_date')->getData(),
                'project' => $proj,
                'user' => $workTime->getUser()
            ]);

            if($find) {
                echo 'IS EXIST';
            } else {
                $workTime->setEmploy($us);
                $em->persist($workTime);
                $em->flush();
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
