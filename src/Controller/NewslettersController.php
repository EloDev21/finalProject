<?php

namespace App\Controller;

use App\Entity\Newsletters\Newsletters;
use App\Entity\Newsletters\Users;
use App\Form\NewslettersUsersType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewslettersController extends AbstractController
{
    /**
     * @Route("/newsletters", name="newsletters_home")
     */
    public function index(Request $request): Response
    {
        $user = new Users();
        $form = $this->createForm(NewslettersUsersType::class, $user);
        $form->handleRequest();

        return $this->render('newsletters/index.html.twig', [
            'form' => $form,
        ]);
    }
}
