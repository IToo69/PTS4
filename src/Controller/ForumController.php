<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;

class ForumController extends AbstractController
{
    /**
     * @Route("/forum", name="forum")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);

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
    public function create(){
        return $this->render('forum/create.html.twig');
    }

    /**
     * @Route("/forum/{id}", name="forum_show")
     */
    public function show($id){
        $repo=$this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($id);
        return $this->render('forum/show.html.twig',[
            'article' => $article
        ]);
    }

}