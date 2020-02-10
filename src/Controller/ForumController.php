<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article;
use App\Repository\ArticleRepository;



class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index( ArticleRepository $repo)
    {
        $articles = $repo->findAll();
        
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(){
        return $this->render('forum/home.html.twig');
    }

    /**
     * @Route("/forum/new", name="forum_create")
     */
    public function create(Request $request, ObjectManager $manager){
        $article = new Article();

        $form = $this->createFormBuilder($article)
                     ->add('titre')
                     ->add('contenu')
                     ->add('image')
                     ->getForm();

        $form->handleRequest()($request);

        if($form->isSubmitted() && $form->isValid()){
            $article->setCreatedAt(new \DateTime());

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', [ 'id' => $article->getId()]);
        }
 

        return $this->render('forum/create.html.twig',[
            'formArticle' => $form->createView()
        ]);
    }

    /**
     * @Route("/forum/{id}", name="forum_show")
     */
    public function show(Article $article){
        return $this->render('forum/show.html.twig',[
            'article' => $article
        ]);
    }

}