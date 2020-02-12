<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Entity\Commentaire;


class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = \Faker\Factory::create('fr_FR');

        //Créer 3 catégories fake

        for($i=1;$i<=3;$i++){
            $categorie = new Categorie();

            $categorie->setTitre($faker->word())
                      ->setDescription($faker->sentence());

            $manager->persist($categorie);

            //Créer entre 4 et 6 articles
            for($j =1; $j<=mt_rand(4,6);$j++){
                $article = new Article();

                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                $article->setTitre($faker->sentence())
                        ->setContenu($content)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategorie($categorie);
    
                $manager->persist($article);

                //Créer entre 4 et 6 commentaires
                for($k=1;$k<=mt_rand(4, 10);$k++){
                    $commentaire = new Commentaire();

                    $contenu = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';

                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $commentaire->setAuteur($faker->name)
                                ->setContenu($contenu)
                                ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                                ->setArticle($article);

                    $manager->persist($commentaire);
                }
            }
        }
        $manager->flush();
    }
}
