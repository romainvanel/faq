<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ADMIN_ACCESS', null, 'Vous ne pouvez pas accéder à cette page')]
#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{
    #[Route('', name: '')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findby(
            [],
            ['nom' => 'ASC']
        );
        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/role', name: '_user_role')]
    public function roleAdmin(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', "L'utilisateur {$user->getNom()} est maintenant un administrateur");

        return $this->redirectToRoute('app_admin');
    }

    // methode pour supprimer utilisateur sans réutiliser la fonction delete dans le UserController
    // #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete')]
    // public function deleteUser(User $user, Request $request, EntityManagerInterface $entityManager): RedirectResponse
    // {
    //     $token = $request->request->get('_token');
    //     $method = $request->request->get('_method');

    //     if($method === 'DELETE' && $this->isCsrfTokenValid('delete_user-'.$user->getId(), $token))
    //     {
    //         // Suppression de l'avatar
    //         $filesystem = new Filesystem();
    //         $fileName = $user->getAvatar();
    //         if ( $fileName!== null && $fileName !== "default.png") {
    //            $filesystem->remove($fileName);
    //         }

    //         $entityManager->remove($user);
    //         $entityManager->flush();

    //         $this->addFlash('success', "l'utilisateur a bien été supprimé");
    //     }

    //     return $this->redirectToRoute('app_admin');
    // }
}
