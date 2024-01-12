<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;


;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        // Instance de faker
        $faker = Faker\Factory::create();

        // Création de 250 questions
        for ($i = 0; $i < 250; $i++) {
            // Génère un nombre aléatoire entre 0 et 49 (coorespondant à la boucle dans UserFixtures)
            $userId = rand(0, 49);
            // $number = $faker->numberBetween(0, 49);

            $question = new Question();
            $question->setTitre("{$faker->sentence} ?");
            $question->setDateCreation($faker->dateTimeBetween('-5 years', '-2 months'));
            $question->setContenu($faker->paragraph);
            // On récupère les données User grace à la référence dans le UserFixtures
            $question->setUser($this->getReference("user-$userId"));

            // Persiste les données
            $manager->persist($question);

            $this->addReference("question-$i", $question);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
