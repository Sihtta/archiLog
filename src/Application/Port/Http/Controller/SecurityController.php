<?php

namespace App\Application\Port\Http\Controller;

use App\Domain\User\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    /**
     * Gère la connexion des utilisateurs.
     */
    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('pages/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Gère la déconnexion (Symfony s'en occupe automatiquement via le firewall).
     */
    #[Route('/deconnexion', name: 'security.logout')]
    public function logout() {}

    /**
     * Gère l'inscription d'un nouvel utilisateur.
     */
    #[Route('/inscription', 'security.registration', methods: ['GET', 'POST'])]
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']); // Assigne un rôle par défaut à l'utilisateur
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $user->getPlainPassword();

            // Hachage du mot de passe avant sauvegarde en base de données
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a bien été créé.');
            return $this->redirectToRoute('security.login');
        }

        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Redirige l'utilisateur après connexion selon son rôle.
     */
    #[Route('/redirect-after-login', name: 'security.redirect_after_login')]
    public function redirectAfterLogin(AuthorizationCheckerInterface $authorizationChecker): RedirectResponse
    {
        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        return $this->redirectToRoute('home.index');
    }
}