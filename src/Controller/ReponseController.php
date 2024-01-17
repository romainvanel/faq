<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    #[IsGranted('REPONSE_EDIT', 'reponse', "Vous ne pouvez pas éditer cette réponse")]
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

    /**
     * Supprimer une réponse
     */
    #[IsGranted('REPONSE_DELETE', 'reponse', 'Vous ne pouvez pas supprimer cette réponse')]
    #[Route('/reponse/{id}/delete', name:'app_reponse_delete', requirements: ['id' => '\d+'])]
    public function deleteReponse(Reponse $reponse, Request $request): RedirectResponse
    {
        $token = $request->request->get('_token');
        $method = $request->request->get('_method');

        if ($method === 'DELETE' && $this->isCsrfTokenValid('reponse_delete', $token)) {
            $this->entityManager->remove($reponse);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réponse à bien été supprimée');
        } else {
            $this->addFlash('error', "Vous ne pouvez pas supprimer cette réponse");
        }

        return $this->redirectToRoute('app_question_reponses', [
            'id' => $reponse->getQuestion()->getId()
        ]);
    }
}
