<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/picture')]
class PictureController extends AbstractController
{
    #[Route('/', name: 'app_picture_index', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PictureRepository $pictureRepository,EntityManagerInterface $entityManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture, ['add'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère la valeur du champ d'upload (pictureFile)
            $pictures = $form->get('pictureFile')->getData();          
            $message = "";
            $dup=[];

            // pour chacque image ($pict) dans le tableau $pictures
            foreach($pictures as $pict){                                    
                $picture = new Picture();
                // on attribue un nom de fichier unique à l'image téléchargée
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $pict->getClientOriginalExtension();                
                // on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
                // affecte le nom de fichier calculé à la propriété 'pictureFile' de l'entité Picture
                $picture->setPictureFile($nomPict);
                //on récupère le nom de fichier original de l'image
                $name = $pict->getClientOriginalName();
        
                // on vérifie qu'une image avec ce nom n'existe pas déjà  
                // s'il existe une image avec le même titre dans la bdd
                if ($pictureRepository->findOneByTitle($name)) {
                    // on stoppe le traitement pour cette image et on repart en haut de la boucle pour l'itération suivante
                    continue;
                }else {
                    // on l'affecte à la propriété title
                    $picture->setTitle($name);                    
                    // on enregistre en bdd (les infos de l'image)
                    $entityManager->persist($picture);
                    $entityManager->flush();
                    
                    // on enregistre l'image dans le répertoire uploads/pictures (image physique)
                    $pict->move(
                        $this->getParameter('pictures_directory'),
                        $nomPict
                    ); // EO move     
                    
                    $pictureRepository->add($picture, true);
                }                    
            }  //EO foreach
           
            return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);

        }// EO if form submitted

        return $this->renderForm('picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }// EO new



    #[Route('/{id}', name: 'app_picture_show', methods: ['GET'])]
    public function show(Picture $picture): Response
    {
        return $this->render('picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        $form = $this->createForm(PictureType::class, $picture, ['edit' => true]);
        $form->handleRequest($request);

        // $form->remove('pictureFile');

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureRepository->add($picture, true);

            return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_picture_delete', methods: ['POST'])]
    public function delete(Request $request, Picture $picture, PictureRepository $pictureRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $pictureRepository->remove($picture, true);
        }

        return $this->redirectToRoute('app_picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
