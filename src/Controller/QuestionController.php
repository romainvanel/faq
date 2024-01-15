<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }
    /**
     * Récupère une seul question et toutes ses réponses
     */
    #[Route('/question/{id}', name: 'app_question_reponses', requirements: ['id' => '\d+'])]
    public function getQuestionReponses(Question $question, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $reponse = new Reponse();

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $reponse->setDateCreation(new \DateTime());
            $reponse->setUser($user);
            $reponse->setQuestion($question);
            $this->entityManager->persist($reponse);
            $this->entityManager->flush();

        $this->addFlash('success', "Votre réponse à bien été ajoutée");
        }

        return $this->render('question/questionReponses.html.twig', [
            'question' => $question, 
            'formAddReponse' => $form
        ]);
    }

}
