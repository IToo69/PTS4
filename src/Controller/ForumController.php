<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Repository\ArticleRepository;
use App\Repository\CategorieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Commentaire;
use App\Entity\Utilisateur;
use App\Form\CommentType;




class ForumController extends AbstractController
{

    /**
     * @Route("/forum/categorie/{id}", name="forum_categorie")
     */
    public function categorie(Categorie $categorie){
        

        return $this->render('forum/categorie.html.twig', ['categorie' => $categorie]);
    }

    /**
     * @Route("/forum/profil/{id}", name="forum_profil")
     */
    public function profil(Utilisateur $user){
        //$utilisateur=$this->getUser();
        
        return $this->render('forum/profil.html.twig', ['user' => $user]);
    }

    

    /**
     * @Route("/forum", name="forum")
     */
    public function index( ArticleRepository $repo)
    {
 
 
            $articles = $repo->findBy([], ['createdAt' => 'DESC']); 
        

        
        return $this->render('forum/index.html.twig', [
            'controller_name' => 'ForumController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(EntityManagerInterface $manager, CategorieRepository $repo, ArticleRepository $repos){

        $articles = $repos->findAll();
        $categories = $repo->findAll();

        if($this->getUser() != null){
        $user=$this->getUser();
        $user->setLastCo(new \DateTime());
        
        $manager->persist($user);
        $manager->flush();
        }
        return $this->render('forum/home.html.twig', ['categories' => $categories, 'articles' => $articles]);
    }

    /**
     * @Route("/forum/new", name="forum_create")
     * @Route("/forum/{id}/edit", name="forum_edit")
     */
    public function form(Article $article = null,Request $request, EntityManagerInterface $manager){

        $creer=false;
        if(!$article){
            $article = new Article();
            $creer=true;
        }

        $form = $this->createFormBuilder($article)
                     ->add('titre')
                     ->add('categorie', EntityType::class, [
                         'class' => Categorie::class,
                         'choice_label' => 'titre'
                     ])
                     ->add('contenu')
                     ->getForm();

        $form->handleRequest($request);
        $user=$this->getUser();    
        if($creer == false){
            if($user != $article->getUtilisateur()){
                return $this->redirectToRoute('forum');
            }
        }            

        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
                $article->setUtilisateur($this->getUser());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('forum_show', [ 'id' => $article->getId()]);
        }
 

        return $this->render('forum/create.html.twig',[
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/forum/{id}", name="forum_show")
     */
    public function show(Article $article, Request $request, EntityManagerInterface $manager){
        $comment = new Commentaire();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $test=$this->getUser();

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article)
                    ->setUtilisateur($test);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('forum_show', ['id' =>$article->getId()]);
        }

        return $this->render('forum/show.html.twig',[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }

    


}