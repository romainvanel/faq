<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReponseController extends AbstractController
{

public function __construct(
    private EntityManagerInterface $entityManager
)
{
    
}

    /**
     * Editer une réponse
     */
    // Sécurisation de la réponse
    #[Route('REPONSE_EDIT', 'reponse', "Vous ne pouvez pas éditer cette réponse")]
    #[Route('/reponse/{id}/edit', name: 'app_reponse_edit')]
    public function editReponse(Request $request, Reponse $reponse): Response
    {
        /**
         * Sécurisation de la page. Seul un utilisateur connecté peut modifier ses propres réponses
         * autre méthode
         */
        // if (!$this->isGranted('REPONSE_EDIT', $reponse)) {
        //     throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette réponse");
        // }

        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reponse->setDateEdition((new \Datetime()));

            $this->entityManager->persist($reponse);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre réponse a bien été ajoutée");

            return $this->redirectToRoute('app_question_reponses', [
                'id' => $reponse->getQuestion()->getId()
            ]);
        }

        return $this->render('/reponse/editReponse.html.twig', [
            'formEditReponse' => $form,
        ]);
    }
}
