<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Permet d'instancier des class dans toute le controlleur
     */
    public function __construct(
        private QuestionRepository $questionRepository
    )
    {
        
    }

    /**
     * Récupère toutes les questions et les ordre par date décroissante
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $questions = $this->questionRepository->findby(
            [],
            ['dateCreation' => 'DESC']
        );

        return $this->render('home/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * Récupère une seul question à l'aide de son id
     */
    #[Route('/question/{id}', name: 'app_one_question', requirements: ['id' => '\d+'])]
    public function getOneQuestion(Question $question): Response
    {
        return $this->render('home/question.html.twig', [
            'question' => $question
        ]);
    }
}
