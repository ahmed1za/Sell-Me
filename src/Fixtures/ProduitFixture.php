<?php

namespace App\Fixtures;

use App\Entity\Categories;
use App\Entity\Image;
use App\Entity\Produit;
use App\Entity\User;
use App\Repository\CategoriesRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProduitFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
     $faker = Factory::create();

        $lienImage = realpath(__DIR__ . '/../../public/imageProduit');


        $formatImages = ['jpg','jpeg','png','avif','webp'];
        $pattern = $lienImage . '/*.{'.implode(',' ,$formatImages).'}';

        $imagefiles = glob($pattern,GLOB_BRACE);
        echo "Chemin des images : " . $lienImage . PHP_EOL;




     $users = [];
    for ($a = 0 ; $a<20 ; $a++){
        $user = new User();
        $user->setNom($faker->firstName);
        $user->setPrenom($faker->lastName);
        $user->setRoles((array)"ROLE_USER");
        $user->setNature($faker->randomElement(["particulier", "professionnel"]));
        $user->setEmail($faker->email);
        $user->setPassword($faker->password);

        $users[]= $user;

        $manager->persist($user);
    }

    $categories = [];
    for ($b=0;$b<8;$b++){
        $categorie = new Categories();
        $categorie->setNom($faker->domainName);
        $categorie->setDescription($faker->text);

        $categories[]=$categorie;
        $manager->persist($categorie);
    }

    $manager->flush();

     for ($i = 0 ;$i< 200;$i++){
         $produit = new Produit();
         $produit->setNom($faker->word);
         $produit->setDiscription($faker->text);
         $produit->setEtat($faker->randomElement(['Neuf', 'Occasion']));
         $produit->setPrix($faker->randomFloat(2, 1, 3000));
         $produit->setQuantite($faker->numberBetween(1,100));
         $produit->setDateDeCreation($faker->dateTimeBetween('-1 year', 'now'));
         $produit->setDateDeModification($faker->dateTimeBetween($produit->getDateDeCreation(),'now'));

         $categorie = $faker->randomElement($categories);
         $produit->setCategorie($categorie);

         $vendeur = $faker->randomElement($users);
         $produit->setVendeur($vendeur);

         $produit->setLivraison($faker->boolean);

         $cheminImage = $faker->randomElement($imagefiles);
         $nomImages = basename($cheminImage);
         $produit->setImage($nomImages);

         $nombreImages= $faker->numberBetween(1,5);
         for ($j=0; $j< $nombreImages; $j++){
             $cheminImage= $faker->randomElement($imagefiles);
             $nomImages = basename($cheminImage);
             $image = new Image();
             $image->setImage($nomImages);
             $produit->addImage($image);
         }

         $manager->persist($produit);
         $manager->flush();

         }

    }
}