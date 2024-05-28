<?php

namespace App\Controller;

use App\Entity\Qualification;
use App\Form\AddQualificationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class QualificationController extends AbstractController
{

    #[Route('/qualification', name: 'app_qualification')]
    public function index(ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $em = $doctrine->getManager();
        $listQualifications = $em->getRepository(Qualification::class)->findAll();
        return $this->render('qualification/index.html.twig', [
            'controller_name' => 'Kwalifikaje',
            'listQualifications' => $listQualifications
        ]);
    }

    #[Route('/qualification/add', name: 'app_qualification_add')]
    public function addQualification(Request $request, ManagerRegistry $doctrine): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $qualification = new Qualification();
        $em = $doctrine->getManager();
        $form = $this->createForm(AddQualificationFormType::class, $qualification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($qualification);
            $em->flush();
            return $this->redirectToRoute('app_qualification');
        }

        return $this->render('qualification/add.html.twig', [
            'controller_name' => 'Dodaj Kwalifikacje',
            'addQualificationForm' => $form->createView()
        ]);
    }

    #[Route('/qualification/edit/{id}', name: 'app_qualification_edit')]
public function editQualification(Qualification $qualification, Request $request, EntityManagerInterface $em)
{
    if (!$this->getUser()){return $this->redirectToRoute('app_login');}
    $form = $this->createForm(AddQualificationFormType::class, $qualification);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($qualification);
        $em->flush();
        $this->addFlash('success', 'Poprawiono kwalifikację');
        return $this->redirectToRoute('app_qualification');
    }
    return $this->render('qualification/edit.html.twig', [
        'controller_name' => 'Edytuj kwalifikację',
        'addQualificationForm' => $form->createView()
    ]);
}

}
