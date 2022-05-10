<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\AddWorkerType;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $listUsers = $em->getRepository(User::class)->findBy(['active' => '1']);
        return $this->render('user/index.html.twig', [
            'controller_name' => 'Użytkownicy',
            'listUsers' => $listUsers
        ]);
    }
    #[Route('/user/addWorker', name: 'app_user_add_worker')]
    public function addWorker(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $em = $doctrine->getManager();
        $form = $this->createForm(AddWorkerType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $firstName = $form->get('first_name')->getData();
            $lastName = $form->get('last_name')->getData();
            $email = $this->transliteratePolishLower(substr($firstName,0,1)).'.'.$this->transliteratePolishLower($lastName).'@elbitech.pl';
            $passGen = $this->randomPassword(15);
            $user->setEmail($email);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $passGen
                    )
                );
            $user->setActive('1');
            $user->setRoles(['ROLE_USER']);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/addWorker.html.twig', [
            'controller_name' => 'Dodaj nowego pracownika',
            'addWorkerForm' => $form->createView()
        ]);
    }

    #[Route('/user/edit/{id}', name: 'app_user_edit')]
    public function editUser(User $user, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Poprawiono Dane użytkownika');
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/edit.html.twig', [
            'controller_name' => 'Edytuj Użytkownika',
            'editUserForm' => $form->createView()
        ]);
    }

    public function transliteratePolishLower($text)
    {
    $alias = mb_strtolower($text,"UTF-8");
    $alias = str_replace(array('ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż',',', ':', ';', ' '), array('a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z','', '', '', '-'), $alias);
    $alias = preg_replace('/[^0-9a-z\-]+/', '', $alias);
    $alias = preg_replace('/[\-]+/', '-', $alias);
    $alias = trim($alias,'-');
    return $alias;
    }

    public function randomPassword($lenght) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$&';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $lenght; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
