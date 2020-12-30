<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Client;
use App\Entity\Adresse;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProduitFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for ($i=1; $i <= 10; $i++) {
            $adresse = new Adresse();

            $libelle = $faker->streetAddress;

            $adresse->setLibelle($libelle)
                    ->setClient((new Client())->getId(mt_rand(1,5)));

            $manager->persist($adresse);
        }

        for ($i=1; $i <= 10; $i++) {
            $client = new Client();

            $nom = $faker->lastName;
            $prenom = $faker->firstName;

            $client->setNom($nom)
                    ->setPrenom($prenom);

            $manager->persist($client);
        }

        for($j =1; $j <= mt_rand(2,10); $j++) {
            $categorie = new Categorie();

            $libelle = $faker->word;
            $categorie->setLibelle($libelle);

           
                for ($i=1; $i <= 10; $i++) {
                    $produit = new Produit();
        
                    $designation = $faker->word;
                    $couleur = $faker->word;
        
                    $produit->setDesignation($designation)
                            ->setPrix(mt_rand(99, 599))
                            ->setCouleur($couleur)
                            ->setCategorieProduit($categorie);
                            
                    $manager->persist($categorie);
                }

                $users = [];
                $genres = ['male' , 'female'];

                for ($k=1; $k <= 10; $k++) {
                    $user = new User();
                    
                    $genre = $faker->randomElement($genres);

                    $picture ='https://randomuser.me/api/portraits/';
                    $pictureId = $faker->numberBetween(1,99) . '.jpg)';

                    $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

                    $email = $faker->unique()->email;
                    $nom = $faker->firstname($genre);
                    $prenom = $faker->lastname;
                    $date = $faker->dateTimeBetween();
                    $inscription = $faker->dateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null);
        
                    $user->setEmail($email)
                            ->setPassword($this->encoder->encodePassword($user,'password'))
                            ->setNom($nom)
                            ->setPrenom($prenom)
                            ->setDateAnniversaire($date)
                            ->setDateInscription($inscription)
                            ->setIntroduction($faker->sentence(2))
                            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                            ->setPicture($picture)
                            ;
                            
                    $manager->persist($user);
                    $users[] = $user;
                }
                for($i = 1; $i <= 5; $i++) {
                    $ad = new Ad();
        
                    $title = $faker->sentence();
                    $coverImage = $faker->imageUrl(1000, 350);
                    $introduction = $faker->paragraph(2);
                    $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
        
                    $user = $users[mt_rand(0, count($users) -1)];

                    $ad->setTitle($title)
                        ->setCoverImage($coverImage)
                        ->setIntroduction($introduction)
                        ->setContent($content)
                        ->setPrice(mt_rand(49, 579))
                        ->setRooms(mt_rand(1, 4))
                        ->setAuthor($user);
        
                    for($j =1; $j <= mt_rand(1,5); $j++) {
                        $image = new Image();
        
                        $image->setUrl($faker->imageUrl())
                            ->setCaption($faker->sentence())
                            ->setAd($ad);
        
                        $manager->persist($image);
                    }
                      
                    // $product = new Product();
                    $manager->persist($ad);
                }
                
            
            $manager->persist($produit);
            // $product = new Product();
            // $manager->persist($product);
        }
        $manager->flush();
    }
}
