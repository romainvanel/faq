<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
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
