<?php

namespace App\Controller;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\PropertySearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PriceSearch;
use App\Form\PriceSearchType;
;
use App\Repository\ArticleRepository;
use App\Entity\PropertySearch;

class IndexController extends AbstractController
{
    private $entityManager;

    // Injection du gestionnaire d'entités dans le contrôleur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route("/", name: "article_list")]
    public function home(Request $request, ArticleRepository $articleRepository)
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);

        $articles = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $nom = $propertySearch->getNom();
            if ($nom != "") {
                $articles = $articleRepository->findBy(['nom' => $nom]);
            } else {
                $articles = $articleRepository->findAll();
            }
        }

        return $this->render('articles/index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
        ]);
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            
            return $this->redirectToRoute('article_list');
        }
        
        return $this->render('articles/new.html.twig', [
            'form' => $form->createView(),
        ]);
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
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('article_list');
        }
    
        return $this->render('articles/edit.html.twig', [
            'form' => $form->createView()
        ]);
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

    #[Route('/category/newCat', name: 'new_category', methods: ['GET', 'POST'])]
    public function newCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('new_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/art_cat', name: 'article_par_cat', methods: ['GET', 'POST'])]
    public function articlesParCategorie(Request $request, ManagerRegistry $doctrine): Response
    {
        $categorySearch = new CategorySearch();
        $form = $this->createForm(CategorySearchType::class, $categorySearch);
        $form->handleRequest($request);

        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categorySearch->getCategory();
            if ($category !== null) {
                $articles = $category->getArticles();
            } else {
                $articles = $doctrine->getRepository(Article::class)->findAll();
            }
        }

        return $this->render('articles/articlesParCategorie.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
    #[Route('/art_prix', name: 'article_par_prix', methods: ['GET'])]
    public function articlesParPrix(Request $request, EntityManagerInterface $em): Response
    {
        $priceSearch = new PriceSearch();
        $form = $this->createForm(PriceSearchType::class, $priceSearch);
        $form->handleRequest($request);

        $articles = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $articles = $em->getRepository(Article::class)
                           ->findByPriceRange(
                               $priceSearch->getMinPrice(),
                               $priceSearch->getMaxPrice()
                           );
        }

        return $this->render('articles/articlesParPrix.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles
        ]);
    }
}