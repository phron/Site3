<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Picture;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/article')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository, PictureRepository $pictureRepository,EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les images transmises dans le champ d'upload (pictures)
            $pictures = $form->get('pictures')->getData();
            
            // on boucle sur les images uploadées
            foreach($pictures as $picture){
                // on attribue un nom de fichier unique à l'image téléchargée
                $nomPict = date('YmdHis') . "-" . uniqid() . "." . $picture->getClientOriginalExtension();
                
                //on récupère le nom de fichier original de l'image
                $name = $picture->getClientOriginalName();
                
                // on enregistre l'image dans le répertoire uploads/pictures (image physique)
                $picture->move(
                    $this->getParameter('pictures_directory'),
                    $nomPict
                ); // EO move  

                // on enregistre l'image en BDD table Picture (ses infos)
                $pict = new Picture();
                $pict->setTitle($name); 
                $pict->setPictureFile($nomPict);

                // on enregistre l'image dans l'article
                $article -> addPicture($pict);               
            } // EO foreach $pictures

            // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
            $images =  $form->get('savedPictures')->getData();
            
            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
                // on ajoute chaque image sélectionnée à l'article
                $article -> addPicture($image);
            }//EO foreach $images

            // On enregistre l'article 
            // qui va sauvegarder définitivement en bdd les images uploadées et créer les liens dans la table de jointure
            // grâce au 'cascade:['persist] ajouté dans la déclaration de la relation (cf Entity/Article))
            $entityManager->persist($article);
            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        // on récupère les images sélectionnées dans le champ savedPictures (les images issues de la bdd)
        $images =  $form->get('savedPictures')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            // on boucle sur les images du champ savedPictures 
            foreach($images as $image){
            // on ajoute chaque image sélectionnée à l'article
            $article -> addPicture($image);
        }
            $articleRepository->add($article, true);

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

   
    #[Route('/edit/{article_id}/unlinkPicture/{id}', name: 'article_unlinkPicture', methods: ['GET','DELETE'])]
    public function unlinkPicture($article_id, $id, Request $request, ArticleRepository $articleRepository, PictureRepository $pictureRepository, EntityManagerInterface $entityManager ): Response
    {
        $article = $articleRepository->find($article_id);
        $picture = $pictureRepository->find($id);
        $article->removePicture($picture);

        $entityManager->persist($article);
        $entityManager->flush();
       
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        
        return $this->renderForm('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);

    }



    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article, true);
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
