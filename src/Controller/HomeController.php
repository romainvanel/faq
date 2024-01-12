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
     * Accueil - Récupère toutes les questions et les ordre par date décroissante
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
     * Récupère une seul question et toutes ses réponses
     */
    #[Route('/question/{id}', name: 'app_question_reponses', requirements: ['id' => '\d+'])]
    public function getQuestionReponses(Question $question): Response
    {
        return $this->render('question/questionReponses.html.twig', [
            'question' => $question
        ]);
    }
}
