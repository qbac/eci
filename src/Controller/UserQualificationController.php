<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserQualification;
use App\Form\UserQualificationFormType;
use ContainerLA1HnHT\getUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserQualificationController extends AbstractController
{
    #[Route('/user/qualification', name: 'app_user_qualification')]
    public function index(): Response
    {
        return $this->render('user_qualification/index.html.twig', [
            'controller_name' => 'UserQualificationController',
        ]);
    }

    #[Route('/user/qualification/add/{id}', name: 'app_user_qualification_add')]
    public function UserQualificationAdd(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $userQualification = new UserQualification();
        $userQualification->setUser($user);

        $form = $this->createForm(UserQualificationFormType::class, $userQualification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($userQualification);
            $em->flush();

            return $this->redirectToRoute('app_user_card', ['id' => $user->getId()]);
        }
        return $this->render('user_qualification/add.html.twig', [
            'controller_name' => 'Dodaj Kwalifikacje dla ' . $user->getFirstName() . ' ' . $user->getLastName(),
            'user' => $user,
            'addUserQualificationForm' => $form->createView()
        ]);
    }

    #[Route('/user/qualification/edit/{id}', name: 'app_user_qualification_edit')]
    public function UserQualificationEdit(UserQualification $userQualification, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}

        $form = $this->createForm(UserQualificationFormType::class, $userQualification);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nextAction = $form->get('remove')->isClicked() ? 'remove': 'edit';

            if ($nextAction == 'edit'){
                $em->persist($userQualification);
                $em->flush();
                $this->addFlash('success', 'Poprawiono Kwalifikacje użytkownika');
            }

            if ($nextAction == 'remove'){
                $em->remove($userQualification);
                $em->flush();
                $this->addFlash('success', 'Usunięto Wpis');
            }
            return $this->redirectToRoute('app_user_card', ['id' => $userQualification->getUser()->getId()]);
        }
        return $this->render('user_qualification/edit.html.twig', [
            'controller_name' => 'Edytuj Kwalifikacje dla ' . $userQualification->getUser()->getFirstName() . ' ' . $userQualification->getUser()->getLastName(),
            'userQualification' => $userQualification,
            'userQualificationForm' => $form->createView()
        ]);
    }
}
