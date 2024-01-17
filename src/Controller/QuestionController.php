<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\QuestionType;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
    public function getQuestionReponses(Question $question, Request $request, MailerInterface $mailer): Response
    {

        // Formulaire de réponse 
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

            // Si une nouvelle réponse est ajoutée on envoie un mail de confirmation à l'auteur de la question
            // On n'envoie pas d'email si la réponse est postée par l'autre de la question
            if ($reponse->getUser() !== $question->getUser()) {
                $email = (new TemplatedEmail())
                    ->from(new Address('noReply@faq.com', 'FAQ'))
                    ->to(new Address($question->getUser()->getEmail(), $question->getUser()->getNom()))
                    ->subject("Une réponse à votre question vient d'être postée")
                    ->htmlTemplate('reponse/email.html.twig')
                    ->context([
                        'question' => $question,
                        'url' => $this->generateUrl('app_question_reponses', ['id' => $question->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
                    ])
                ;
                $mailer->send($email);                
            }


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
    #[IsGranted('QUESTION_ADD', null, "Veuillez vous connecter pour accéder à cette page")]
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

    /**
     * Edition d'une question
     */
    #[IsGranted('QUESTION_EDIT', 'question', "Vous ne pouvez pas éditer cette réponse")]
    #[Route('/question/{id}/edit', name: 'app_question_edit')]
    public function editQuestion(Question $question, Request $request): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        return $this->render('/question/editQuestion.html.twig', [
            'formEditQuestion' => $form
        ]);
    }

    /**
     * Suppression d'une question
     */
    #[IsGranted('QUESTION_DELETE', 'question', "Vous ne pouvez pas supprimer cette question")]
    #[Route('/question/{id}/delete', name: 'app_question_delete', requirements: ['id' => '\d+'])]
    public function deleteQuestion(Question $question, Request $request): RedirectResponse
    {

        $token = $request->request->get('_token');
        $method = $request->request->get('_method');

        if ($method === 'DELETE' && $this->isCsrfTokenValid('question_delete', $token)) {
            $this->entityManager->remove($question);
            $this->entityManager->flush(); 
            
            $this->addFlash('success', 'Votre question a bien été supprimée');
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer cette question');
        }

        return $this->redirectToRoute('app_home');
    }
}
