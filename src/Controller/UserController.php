<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('USER_ACCESS', null, 'Veuillez vous connecter pour accéder à cette partie' )]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
        
    }
    /**
     * Affiche les infos de l'utilisateur
     */
    #[Route('/user/profile', name: 'app_user_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Editer le profil utilisateur
     */
    #[Route('/user/profile/edit', name: 'app_user_profile_edit')]
     public function editProfile(Request $request, UploadService $uploadService): Response
     {
        // Permet de retyper la variable user sinon par defaut le $this->getUser est associé à la classe UserInterface et pas l'entité User - Pasobligatoire mais évite l'erreur sur l'ide
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'is_profile' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatarFile')->getData();
            // Si une image a été soumise on l'upload
            if ($avatarFile) {
                $fileName = $uploadService->upload($avatarFile, $user->getAvatar());
                $user->setAvatar($fileName);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', "Votre profil à bien été mis à jour");

            // return $this->redirectToRoute('app_user_profile', [
            //     'id' => $user->getId()
            // ]);
        }
        return $this->render('/user/editProfile.html.twig', [
            'formEditUser' => $form
        ]);
     }

     /**
      * Supprimer un utilisateur
      */
     #[Route('/user/profile/remove', name: 'app_user_profile_remove')]
      public function removeUser(Request $request): RedirectResponse
      {
        // Récupération du jeton CSRF du formulaire
        $token = $request->request->get('_token');
        // Récupération de la méthode indiqué dans le formulaire
        $method = $request->request->get('_method');

        // Vérifie si le nom du token que l'on a choisi dans le formulaire est valide et si la méthode est bien delete (choisi dans le input)
        if ($method === 'DELETE' && $this->isCsrfTokenValid('delete_user', $token)) {
        /** @var User $user */
        $user = $this->getUser();

        // Suppression de la photo
        $filesystem = new Filesystem();
        $fileName = $user->getAvatar();
        if ( $fileName!== null && $fileName !== "default.png") {
           $filesystem->remove($fileName);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // invalidation de la session utilisateur
        $session = $request->getSession();
        $session->invalidate();
        // et annnule aussi le token de securité associé à la session
        $this->container->get('security.token_storage')->setToken(null);

        $this->addFlash('success', "Votre compte a bien ête supprimé");

        return $this->redirectToRoute('app_home');
        }

        // Retour vers la page de profil si le token CSRF est invalide
        $this->addFlash('error', 'Jeton CRSF invalide');

        return $this->redirectToRoute('app_user_profile');
      }
}
