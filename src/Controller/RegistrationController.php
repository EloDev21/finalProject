<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // on instancie un objet de type user
        $user = new User();
        // on set la date de crreation de notre objet user
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        // on cree un formulaire de contact grace à FormBunlde de Symfony de type RegistrationFormType
        $form = $this->createForm(RegistrationFormType::class, $user);
         // on cree une requete grace à la librairie Request de HttpFoundation pour avoir acces au formulaire 
        $form->handleRequest($request);
         // si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                // on obtient password en récuperant les données du formulaire puis grace à bcrypt (pour crypter/hasher les mots de passe)
                // on sécurise le pwd
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // on définit un role par défaut à l'inscription 
            $user->setRoles(['ROLE_USER']);
                // on fait appel à doctrine pour enregistrer en base de données
                $em = $this->getDoctrine()->getManager();
                // on prepare la mise en BDD
                $em->persist($user);
                // on sauvegarde en BDD
                $em->flush();
               // on rajoute un message flash pour informer que l'inscription s'est bien déroulée avec success et on redirige vers la page de connexion
            $this->addFlash('inscription', 'Bravo et Bienvenue. Votre inscription s\'est deroulée avec succès.Vous pouvez à présent vous connecter à votre espace personnel.');
            return $this->redirectToRoute('app_login');
        }
// sinon on redirige vers la page d'inscription et on récupere notre form pour pouvoir l'utilisier dans la vue twig 
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
