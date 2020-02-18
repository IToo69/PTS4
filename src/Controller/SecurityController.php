<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Utilisateur;
use App\Form\InscriptionType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/inscription", name="security_inscription")
     */
    public function inscription(Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder){
        $user = new Utilisateur();

        $form=$this->createForm(InscriptionType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);
            $user->setCreatedAt(new \DateTime());
            $user->setLastCo(new \DateTime());

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('security_login', [ 'id' => $user]);
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout(){
        
    }
}
