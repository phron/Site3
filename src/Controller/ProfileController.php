<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use App\Repository\ProfileRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile_index', methods: ['GET'])]
    public function index(ProfileRepository $profileRepository): Response
    {
        return $this->render('profile/index.html.twig', [
            'profiles' => $profileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'profile_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProfileRepository $profileRepository): Response
    {
        $profile = new Profile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }


    // ******************************************************************************************************  
    //                                      SHOW PROFIL
    // ******************************************************************************************************



    #[Route('/profil', name:'profile_show')]
    public function show(ProfileRepository $profileRepository) :Response
    {
        return $this->render('profile/show.html.twig',[
            'profile' => $profileRepository->findOneByUserId($this->getUser()),
        ]);
    }

    // ******************************************************************************************************  
    //                                     EDITION PROFIL
    // ******************************************************************************************************


    #[Route('/{id}/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profile $profile, ProfileRepository $profileRepository): Response
    {
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profileRepository->add($profile, true);

            return $this->redirectToRoute('profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    // ******************************************************************************************************  
    //                              SUPPRESSION COMPTE UTILSATEUR (Profile + User)
    // ******************************************************************************************************

    // permet de récupérer la session active
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    // Modification pour que l'action 'delete' supprime le Profile ET le User associé (utilisé pour 'supprimer mon compte")
    #[Route('/{id}', name: 'profile_delete', methods: ['POST'])]
    public function delete(Request $request, Profile $profile, ProfileRepository $profileRepository, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profile->getId(), $request->request->get('_token'))) {
            // on récupère l'utilisateur associé au profil
            $user = $profile->getUser();
            // on supprime le profil
            $profileRepository->remove($profile, true);
            // on supprime le user
            $userRepository->remove($user, true);
            // la suppression ne peut pas fonctionner sur une session active, on doit kill la session pour ne pas avoir d'erreur
            // on récupère la session active
            $session = $this->RequestStack->getSession();
            // on instancie une nouvelle session vierge
            $session = new Session();
            // on kill la session active
            $session->invalidate();
        }

        return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }

    // ******************************************************************************************************  
    //                                     RESET FORMULAIRE PROFIL
    // ******************************************************************************************************

    // profile_reset : remet tous les champs du profil à vide sauf user et updatedAt    
    #[Route('/{id}', name: 'profile_reset', methods: ['GET','POST'])]
    public function reset(Request $request, Profile $profile, ProfileRepository $profileRepository): Response
    {
        
        $profile->setLastName("");
        $profile->setFirstName("");
        $profile->setPhoneNumber("");
        $profile->setAddress("");
        $profile->setAddress2("");
        $profile->setZipcode("");
        $profile->setCity("");
        $profile->setStatus(null);
        $profile->setUpdatedAt( new \DateTimeImmutable('now'));
        
        $profileRepository->add($profile, true);


        return $this->redirectToRoute('profile_edit', ['id' => $profile->getId()], Response::HTTP_SEE_OTHER);
        return $this->renderForm('profile/edit.html.twig', [
            'profile' => $profile,
            // 'form' => $form,
        ]);
        
    }

}