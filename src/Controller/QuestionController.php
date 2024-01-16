<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionType;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuestionController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }
    /**
     * Récupère une seul question et toutes ses réponses et permet d'ajouter une réponse
     */
    #[Route('/question/{id}', name: 'app_question_reponses', requirements: ['id' => '\d+'])]
    public function getQuestionReponses(Question $question, Request $request): Response
    {
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);

        // Clone du formulaire vide dans un nouvel objet
        $emptyForm = clone $form;
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $reponse->setDateCreation(new \DateTime());
            $reponse->setUser($this->getUser());
            $reponse->setQuestion($question);
            $this->entityManager->persist($reponse);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre réponse a bien été ajoutée");

            // On clone notre objet formulaire vide dans l'objet de départ pour afficher le formulaire vide sur la page après validation
            $form = clone $emptyForm;
        }

        return $this->render('question/questionReponses.html.twig', [
            'question' => $question, 
            'formAddReponse' => $form
        ]);
    }

    /**
     * Poser une question
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/question/add', name: 'app_question_add')]
    public function addQuestion(Request $request): Response
    {
        /**
         * Sécurisation de la page :
         * 
         * 1ère méthode : 
         * en injectant Security $security dans la méthode
         * 
         */
        // if (!$security->isGranted('ROLE_USER')) {
        //     return $this->redirectToRoute('app_login');
        // }

        /**
         * 2eme méthode
         */

        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /**
         * 3eme méthode
         * Modification dans le fichier config/package/security.yaml dans la partie access-control
         */

        /**
         * 4eme méthode : 
         * #[IsGranted('ROLE_USER')]
         */

        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setUser($this->getUser());
            $question->setDateCreation(new \DateTime());
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre question est en ligne");

            return $this->redirectToRoute('app_question_reponses', [
                'id' => $question->getId()
            ]);
        }

        return $this->render('question/addQuestion.html.twig', [
            'formAddQuestion' => $form
        ]);
    }

}
