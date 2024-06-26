<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\UserQualification;
use App\Form\AddWorkerType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{

    //#[IsGranted('ROLE_ADMIN',statusCode: 403, message: 'Nie masz dostępu do tej strony')]
    #[Route('/user', name: 'app_user')]
    public function index(ManagerRegistry $doctrine, UserRepository $userRepository): Response
    {
        // if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true) || in_array('ROLE_COORDINATOR', $this->getUser()->getRoles(), true)) {
        //     echo "JEST OK";
        // } else {echo "BRAK DOSTĘPU";}

        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $em = $doctrine->getManager();
        //$listUsers = $em->getRepository(User::class)->find(['active' => '1']);
        $listUsers = $userRepository->getActiveUser();
        //$listUsersUnactive = $em->getRepository(User::class)->findBy(['active' => '0']);
        $listUsersUnactive = $userRepository->getUnactiveUser();
        
        return $this->render('user/index.html.twig', [
            'controller_name' => 'Użytkownicy',
            'listUsers' => $listUsers,
            'listUsersUnactive' => $listUsersUnactive
        ]);
    }
    #[Route('/user/addWorker', name: 'app_user_add_worker')]
    public function addWorker(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
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
    public function editUser(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData())
            {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
            }
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Poprawiono Dane użytkownika');
            return $this->redirectToRoute('app_user');
        }
        return $this->render('user/edit.html.twig', [
            'controller_name' => 'Edytuj dane pracownika',
            'editUserForm' => $form->createView()
        ]);
    }

    #[Route('/user/card/{id}', name: 'app_user_card')]
    public function cardUser(User $user, EntityManagerInterface $em)
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $userDoctrine = $em->getRepository(User::class)->find($user->getId());
        $userQualifications = $em->getRepository(UserQualification::class)->findBy(['user' => $user->getId()]);
        return $this->render('user/cardUser.html.twig', [
            'controller_name' => 'Karta pracownika',
            'user' => $userDoctrine,
            'qualifications' => $userQualifications
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
