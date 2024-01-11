<?php

namespace App\DataFixtures;

use App\Entity\Reponse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
;

class ReponseFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        // Instance de faker
        $faker = Faker\Factory::create();


        // Création de 1000 questions
        for ($i = 0; $i < 1000; $i++) {
            // Récupération d'un utilisateur aléatoire
            $user = $this->getReference("user-{$faker->numberBetween(0, 49)}");

            // Récupération d'une question aléatoire
            $question = $this->getReference("question-{$faker->numberBetween(0, 249)}");
            $dateCreationQuestion = $question->getDateCreation()->format('Y-m-d H:i:s');


            $reponse = new Reponse();
            $reponse->setContenu($faker->realText);
            $reponse->setDateCreation($faker->dateTimeBetween($dateCreationQuestion));

            // Génère un nombre aléatoire entre 0 et 249 (coorespondant à la boucle dans QuestionFixtures)

            $reponse->setQuestion($question);

            $reponse->setUser($user);

            // Persiste les données
            $manager->persist($reponse);
        }

        $manager->flush();
    }

    /**
     * Permet de générer les fixtures User et Question avant les Réponses
     */
    public function getDependencies()
    {
        return [
            QuestionFixtures::class,
            UserFixtures::class,
        ];
    }
    
}
