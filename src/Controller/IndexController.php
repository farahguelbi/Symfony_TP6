<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $entityManager;

    // Injection du gestionnaire d'entités dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="article_list")
     */
    public function home(): Response
    {
        // Récupérer tous les articles de la base de données
        $articles = $this->entityManager->getRepository(Article::class)->findAll();

        // Renvoyer la liste des articles dans la vue
        return $this->render('articles/index.html.twig', ['articles' => $articles]);
    }

    /**
     * @Route("/article/save", name="article_save")
     */
    public function save(): Response
    {
        // Créer un nouvel article
        $article = new Article();
        $article->setNom('Article 1');
        $article->setPrix('1000.00');

        // Sauvegarder l'article dans la base de données
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        // Retourner la réponse avec l'ID de l'article enregistré
        return new Response('Article enregistré avec l\'ID ' . $article->getId());
    }

    /**
     * @Route("/article/new", name="new_article", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();

        // Créer le formulaire pour ajouter un article
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Créer'])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'article dans la base de données
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            // Rediriger vers la liste des articles après l'ajout
            return $this->redirectToRoute('article_list');
        }

        // Afficher le formulaire d'ajout d'article
        return $this->render('articles/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show(int $id): Response
    {
        // Trouver l'article par son ID
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Afficher les détails de l'article
        return $this->render('articles/show.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        // Trouver l'article à modifier
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Créer le formulaire de modification
        $form = $this->createFormBuilder($article)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder les modifications dans la base de données
            $this->entityManager->flush();

            // Rediriger vers la liste des articles après la modification
            return $this->redirectToRoute('article_list');
        }

        // Afficher le formulaire de modification
        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

/**
     * @Route("/article/delete/{id}", name="delete_article", methods={"GET"})
     */
    public function delete(int $id): Response
    {
        // Trouver l'article à supprimer
        $article = $this->entityManager->getRepository(Article::class)->find($id);

        // Vérifier si l'article existe
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        // Supprimer l'article de la base de données
        $this->entityManager->remove($article);
        $this->entityManager->flush();

        // Rediriger vers la liste des articles après la suppression
        return $this->redirectToRoute('article_list');
    }
    
}
