<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Notification\ChangePassword;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\ProfileType;
use App\Service\Cart\CartService;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;

class ProfileController extends AbstractController
{
         
    /**
     * @Route("/profiles", name="profiles")
     */
    public function index(): Response
    {
        // on cree une variable $profiles puis on utilise Doctrine pour acceder à notre BDD 
        // plus précisèment à la table circuits à laquelle on applique la requete SQL findAll()
        // pour afficher tous les elements présents dans notre table user
        // on envoie $profiles à la vue index du dossier profile
        // on voit les parametres profiles et le total à notre vue

        $profiles = $this->getDoctrine()->getRepository(User::class)->findAll();
        // nombre total d'utilsateurs
        $total= count($profiles);
       
        return $this->render('profile/index.html.twig', [
            'profiles' => $profiles,
            'total' => $total
         
            
           
        ]);
    }

    /**
     * @Route("/profiles/remove/{id}", name="profile_remove")
     */
    public function remove($id)
    {
    //   on utilise doctrine et sql pour trouver l'utilisateur en question   
        $profil = $this->getDoctrine()->getRepository(User::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        // on attribue l'id de notre élément séléctionné à la fonction remove
        $em->remove($profil);
        // on enregistre les modifications en BDD
        $em->flush();
        $this->addFlash('message', 'Utilisateur supprimé avec succès!!! ');
        // redirection vers la route nommée profiles
        return $this->redirectToRoute('profiles') ;
    }
    /**
     * @Route("/profile_detail", name="profile_detail")
     */
    public function profile(): Response
    {  

        return $this->render('profile/profile_detail.html.twig');
    }
    /**
     * @Route("/profile/edit", name="profile_edit")
     */
    public function editProfile(Request $request): Response
    {
       
            $user = $this->getUser();
            $form = $this->createForm(EditProfileType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
               
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
            
                $em->flush();
                $this->addFlash('message','Profil mis à jour avec succès');
                return $this->redirectToRoute('profile_detail');
            }
            return $this->render('profile/editprofile.html.twig', [
                'form' => $form->createView()
            ]);
            
           
       
    }
    
    /**
     * @Route("/profile/pass_edit", name="pass_edit")
     */
    public function editPassword(Request $request , UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // pour le traitement on verifie tout dabord quon est en mode post
        if($request->isMethod('POST')){
            
            $em=$this->getDoctrine()->getManager();

    //    on recupere le profil de l'utilisateur
           $user = $this->getUser();
 
        //puis on verifie que les 2 mdp du formulaire sont identiques
        if($request->request->get('pass') == $request->request->get('pass2'))
        {
            // on encode le password
            $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('pass')));
            // on le sauvegarde en base de donnees
            $em->flush();
            // on affiche un retour
            $this->addFlash('message','Votre mot de passe a bien été mis à jour.');

            return $this->redirectToRoute('profile_detail');
        }
        else{
            $this->addFlash('error','Les 2 mots de passe ne correspondent pas. Merci de bien vérifier les champs saisis!');
        }
    }      
        return $this->render('profile/editpass.html.twig');
            
    }

}
