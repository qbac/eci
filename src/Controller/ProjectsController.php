<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectsController extends AbstractController
{
    #[Route('/project', name: 'app_projects')]
    public function index(ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $em = $doctrine->getManager();
        $projectActive = $em->getRepository(Project::class)->findBy(['active' => true]);
        $projectUnactive = $em->getRepository(Project::class)->findBy(['active' => false]);
        return $this->render('projects/index.html.twig', [
            'controller_name' => 'Projekty',
            'projectActive' => $projectActive,
            'projectUnactive' => $projectUnactive
        ]);
    }
    
    #[Route('/project/add', name: 'app_project_add')]
    public function addProject(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $project = new Project();
        $em = $doctrine->getManager();
        $form = $this->createForm(ProjectFormType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($project);
            $em->flush();
            return $this->redirectToRoute('app_projects');
        }
        return $this->render('projects/add.html.twig', [
            'controller_name' => 'Dodaj Projekt',
            'addProjectForm' => $form->createView()
        ]);
    }

    #[Route('/project/edit/{id}', name: 'app_project_edit')]
    public function editProject(Project $project, Request $request, EntityManagerInterface $em)
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $form = $this->createForm(ProjectFormType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'Poprawiono projekt');
            return $this->redirectToRoute('app_projects');
        }
        return $this->render('projects/edit.html.twig', [
            'controller_name' => 'Edytuj Projekt',
            'addProjectForm' => $form->createView()
        ]);
    }
}
