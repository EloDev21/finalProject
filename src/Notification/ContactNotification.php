<?php

namespace App\Notification;

use App\Entity\ContactForm;

use SebastianBergmann\CodeCoverage\Report\Html\Renderer;
use Twig\Environment;


class ContactNotification
{
    /**
     *  @var \Swift_Mailer
     */
    private $mailer;
    /**
     *  @var Environement
     */
    private $renderer;
    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
      
    }

    public function notify(ContactForm $contact)
    {
        // objet du mail
        $message = (new \Swift_Message('Nouveau message from Safari Website '))
            ->setFrom(['noreply@symf.com' => 'Elo Loel'])
            //   ->setFrom($contact->getEmail())
            ->setTo(['contact@email.fr', 'other@domain.org' => 'A name'])
            ->setReplyTo($contact->getEmail())
            //   on peut mettre un message direct comme on peut use un template
            //   text/html pour le formay du body
            ->setBody($this->renderer->render('contact/index.html.twig', [
                'contact' => $contact
            ]), 'text/html');
            $this->mailer->send($message);
    }
}
