<?php

namespace App\Controller;
use App\Notification\ChangePassword;
use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
         
    /**
     * @Route("/profiles", name="profile")
     */
    public function index(): Response
    {
        $profiles = $this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('profile/index.html.twig', [
            'profiles' => $profiles
         
            
           
        ]);
    }
    /**
     * @Route("/profiles/edit", name="profile_edit")
     */
    public function editProfile(Request $request): Response
    {
       
            $user = $this->getUser();
            $form = $this->createForm(ProfileType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
               
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
            
                $em->flush();
                return $this->redirectToRoute('profile');
            }
            return $this->render('profile/editprofile.html.twig', [
                'form' => $form->createView()
            ]);
            
           
       
    }
    
    /**
     * @Route("/profiles/pass/edit", name="pass_edit")
     */
    public function editpassword(Request $request , UserPasswordEncoderInterface $passwordEncoder): Response
    {
       
        $user = $this->getUser();
 
        return $this->render('profile/editpass.html.twig');
            
           
    }

}
