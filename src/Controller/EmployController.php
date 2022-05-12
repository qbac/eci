<?php

namespace App\Controller;

use App\Entity\Employ;
use App\Form\EmployType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployController extends AbstractController
{
    #[Route('/employ', name: 'app_employ')]
    public function index(ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $em = $doctrine->getManager();
        $employActive = $em->getRepository(Employ::class)->findBy(['active' => true]);
        $employUnactive = $em->getRepository(Employ::class)->findBy(['active' => false]);
        return $this->render('employ/index.html.twig', [
            'controller_name' => 'Sposoby zatrudnienia',
            'employActive' => $employActive,
            'employUnactive' => $employUnactive
        ]);
    }

    #[Route('/employ/edit/{id}', name: 'app_employ_edit')]
    public function editEmploy(Employ $project, Request $request, EntityManagerInterface $em)
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $form = $this->createForm(EmployType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'Poprawiono sposób zatrudnienia');
            return $this->redirectToRoute('app_employ');
        }
        return $this->render('employ/edit.html.twig', [
            'controller_name' => 'Edytuj sposób zatrudnienia',
            'addEmployForm' => $form->createView()
        ]);
    }

    #[Route('/employ/add', name: 'app_employ_add')]
    public function addEmploy(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $employ = new Employ();
        $em = $doctrine->getManager();
        $form = $this->createForm(EmployType::class, $employ);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($employ);
            $em->flush();
            return $this->redirectToRoute('app_employ');
        }
        return $this->render('employ/add.html.twig', [
            'controller_name' => 'Dodaj sposób zatrudnienia',
            'addEmployForm' => $form->createView()
        ]);
    }
}
