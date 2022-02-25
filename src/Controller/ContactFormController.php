<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\ContactForm;
use App\Notification\ContactNotification;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class ContactFormController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,  \Swift_Mailer $mailer): Response
    {
        $contactF = new ContactForm();
        $contactF->setCreatedAt(new \DateTimeImmutable('now'));
        $formContact = $this->createForm(ContactFormType::class, $contactF);
        $formContact->handleRequest($request);
        if ($formContact->isSubmitted() && $formContact->isValid()) 
        {
            // snding the message with swiftmailer to the admin-team
            $message = (new \Swift_Message('Mail de contact - SeneSAFARI'))
            ->setFrom($contactF->getEmail())
            ->setTo('senesafari@example.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/contact.html.twig',
                    ['contactF' => $contactF]
                ),
                'text/html'
            );
            $mailer->send($message);
            // saving the message in the entity
            $contactF = $formContact->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($contactF);
            $em->flush();

            // $notification->notify($contactF);
            $this->addFlash('contact_email', 'Votre mail a été envoyé avec avec succès! Notre équipe vous reviendra dans les plus brefs délais. ');
            return $this->redirectToRoute('home');
        }
        return $this->render('contact/index.html.twig', [
            'formContact' => $formContact->createView(),
        ]);
    }
}
