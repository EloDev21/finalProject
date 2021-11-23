<?php

namespace App\Controller;
use App\Form\ContactType;
use App\Entity\Contact;
use App\Notification\ContactNotification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, ContactNotification $notif): Response
    {
        $contact = new Contact();
         $formContact = $this->createForm(ContactType::class, $contact);
        $formContact->handleRequest($request);
        if($formContact->isSubmitted() && $formContact->isValid())
        {
          $notif->notify($contact);
          
          $em= $this->getDoctrine()->getManager();
          $em->persist($contact);
          $em->flush();
          
          return $this->redirectToRoute('home');
          $this->addFlash('mail_envoye', 'Votre mail a été envoyé  avec succès et notre équipe vous reviendra dans les plus breefs délais!!! ');

        }
       
        return $this->render('contact/index.html.twig', [
            'formContact'=> $formContact->createView()
        ]); 
    }
}
