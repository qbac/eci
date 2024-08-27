<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserQualification;
use App\Form\UserQualificationFormType;
use App\Repository\UserQualificationRepository;
use ContainerLA1HnHT\getUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

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
    public function UserQualificationEdit(int $id, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}

        $existingUserQualification = $em->getRepository(UserQualification::class)->find($id);
        //echo 'Nazwa pliku przed submit i form $fileOld' .$existingUserQualification->getFileName();
        $fileOld = $existingUserQualification->getFileName();
        $form = $this->createForm(UserQualificationFormType::class, $existingUserQualification);
        $form->handleRequest($request);
        //echo 'Nazwa pliku przed submit PO form $fileNew' .$existingUserQualification->getFileName();
        $fileNew = $existingUserQualification->getFileName();

        if ($form->isSubmitted() && $form->isValid()) {
            
            $fi = $existingUserQualification->getFileName();
            //echo 'Nazwa pliku po submit $fi ' . $fi;
            $nextAction = $form->get('remove')->isClicked() ? 'remove': 'edit';

            $pictureFileName = $form->get('fileName')->getData();

            if ($nextAction == 'edit'){
                //whether a file to be uploaded has been selected
                //echo '<br> Popraw';
                //if ($userQualification->getFileName()){
                    $pictureFileName = $form->get('fileName')->getData();
                    if (!is_null($pictureFileName)) {
                        try {
                            //echo '<br> Jest plik';
                        //get file name
                        $originalFileName = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                        //delete polish character, and change large letters to small
                        $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFileName);
                        $newFileName = $safeFileName . '-' . uniqid() . '.' . $pictureFileName->guessExtension();
                        $pictureFileName->move('upload/qualification/', $newFileName);
                        $existingUserQualification->setFileName($newFileName);
                        $existingUserQualification->setUploadedAt(new \DateTimeImmutable());
                        
                    } catch (\Exception $e) {
                            $this->addFlash('error', 'Nie dodano zdjęcia');
                        }

                    } else {
                        // Keep the old file name if no new file is uploaded
                        //echo '<br> Brak pliku ' . $fileOld;
                        if ($fileOld) {
                            $existingUserQualification->setFileName($fileOld);
                        }
                        
                    }
                //}
                //$existingUserQualification->setFileName($fileOld);
                //echo '<br> Nazwa pliku przez flush ' . $existingUserQualification->getFileName();
                $em->persist($existingUserQualification);
                $em->flush();
                $this->addFlash('success', 'Poprawiono Kwalifikacje użytkownika.');
                return $this->redirectToRoute('app_user_card', ['id' => $existingUserQualification->getUser()->getId()]);
            }

            if ($nextAction == 'remove'){
                $em->remove($existingUserQualification);
                $em->flush();
                $this->addFlash('success', 'Usunięto Wpis');
                return $this->redirectToRoute('app_user_card', ['id' => $existingUserQualification->getUser()->getId()]);
            }
        }

        return $this->render('user_qualification/edit.html.twig', [
            'controller_name' => 'Edytuj Kwalifikacje dla ' . $existingUserQualification->getUser()->getFirstName() . ' ' . $existingUserQualification->getUser()->getLastName(),
            'userQualification' => $existingUserQualification,
            'userQualificationForm' => $form->createView()
        ]);
    }

    #[Route('/user/qualification/download/{id}', name: 'app_user_qualification_download')]
    public function UserQualificationDownload(UserQualification $userQualification, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $response = new BinaryFileResponse($this->getParameter('app.path.file.qualification') . $userQualification->getFileName());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$userQualification->getFileName());
        return $response;
    }

    #[Route('/user/qualification/removefile/{id}', name: 'app_user_qualification_removefile')]
    public function UserQualificationRemoveFile(UserQualification $userQualification, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()){return $this->redirectToRoute('app_login');}
        $fileManager = new Filesystem();
        $fileName = $userQualification->getFileName();
        $fileManager->remove('upload/qualification/' . $userQualification->getFileName());
        $userQualification->setFileName(NULL);
        $userQualification->setUploadedAt(new \DateTimeImmutable());
        $em->persist($userQualification);
        $em->flush();
        $this->addFlash('success', 'Usunięto plik ' . $fileName);
        return $this->redirectToRoute('app_user_qualification_edit', ['id' => $userQualification->getId()]);
    }


 // API   
    #[Route('/api/user/qualification/remaindbefore', name: 'api_user_qualification_remaindbefore', methods:'GET')]
    public function RemaindUserQualification(UserQualificationRepository $userQualificationRepository): Response
    {
        $resultTotal = $userQualificationRepository->remaindBefore();
        return $this->json($resultTotal);
    }
}
