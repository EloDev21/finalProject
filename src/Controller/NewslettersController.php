<?php

namespace App\Controller;

use App\Entity\Newsletters\Newsletters;
use App\Entity\Newsletters\Users;
use App\Form\NewslettersType;
use App\Form\NewslettersUsersType;
use App\Repository\Newsletters\NewslettersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewslettersController extends AbstractController
{
    /**
     * @Route("/newsletters", name="newsletters")
     */
    public function index(Request $request , \Swift_Mailer $mailer): Response
    {
        $user = new Users();
        $form = $this->createForm(NewslettersUsersType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // creation du token 
            $token =hash('sha256',uniqid());
            $user->setValidationToken($token);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $em->flush();
            $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
            ->setSubject('Inscription à la newsletter')
            ->setFrom('senesafari@example.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/newsletter.html.twig',
                    ['user' => $user,
                        'token' =>$token
                    ]
                ),
                'text/html'
            );
            $mailer->send($message);
            $this->addFlash('activation_newsletter','Bravo votre compte vient d\'être acitvé.Nous sommes ravis de vous compter parmis nos memebres.');
            // on garde le token . on le supprime pas pour le désabonnement
            $this->addFlash('attente','Votre inscription à la Newsletter en attente de validation. Merci de consulter votre courriel.');
            return $this->redirectToRoute('home');
        }

        

        return $this->render('newsletters/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // on créée le lien de redirection ou confirmation
    /**
     * @Route("newsletters_confirm/{id}/{token}", name="newsletters_confirm")
     */
    public function confirm(Users $user , $token, \Swift_Mailer $mailer): Response
    {
        // si il na pas de validationtoken on le cree
        if($user->getValidationToken() != $token ){
            throw $this->createNotFoundException('Page non trouvée.');
        }
            $user->setIsValid(true);
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
          
            return $this->redirectToRoute('home'); 
        }
    /**
     * @Route("newsletters/prepare", name="newletters_prepare")
     */
    public function prepare(Request $request): Response
    {
        // si il na pas de validationtoken on le cree
        $newsletter = new Newsletters();
        $form = $this->createForm(NewslettersType::class, $newsletter);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newsletter);
            $em->flush();
            return $this->redirectToRoute('newsletters_list');
        }
            $this->addFlash('creation_newsletter','Votre newsletter vient d\'être créée avec succès.');
            return $this->render('newsletters/prepare.html.twig',[
                'form' => $form->createView()
            ]);
         }
    /**
    * @Route("newsletters/list", name="newsletters_list")
    */
    public function list (NewslettersRepository $newsletters):Response
         {
                return $this->render('newsletters/list.html.twig',[
                    'newsletters' => $newsletters->findAll()
                ]);
         }
    /**
    * @Route("newsletters/send/{id}", name="newsletters_send")
    */
    public function send (NewslettersRepository $newsletters, \Swift_Mailer $mailer):Response
         {
             
             $users = $newsletters->getCategories()->getUsers();
           
               
                foreach ($users as $user) {
                    if($user->getIsValid())
                    {
                        $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
                        ->setSubject('Inscription à la newsletter')
                        ->setFrom('senesafari@example.com')
                        ->setTo($user->getEmail())
                        ->setBody(
                            $this->renderView(
                                // templates/emails/registration.html.twig
                                'emails/newsletter.html.twig',
                                ['user' => $user,

                                'newsletters' =>$newsletters
                                  
                                ]
                            ),
                            'text/html'
                        );
                        $mailer->send($message);

                    }
                }
                return $this->redirectToRoute('newsletters_list');
         }
          
        

}

