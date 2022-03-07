<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function index(): Response
    {
        // on génére une exception
        // throw $this->createNotFoundException('Page perdue ou inexistante!');
        return $this->render('home/index.html.twig');
    }
    /**
     * @Route("/mentions_legales", name="mentions_legales")
     */
    public function mentions(): Response
    {
        // on génére une exception
        // throw $this->createNotFoundException('Page perdue ou inexistante!');
        return $this->render('mentions/index.html.twig');
    }
    /**
     * @Route("/conditions-d-utilisation", name="conditions-d-utilisation")
     */
    public function cgu(): Response
    {
        // on génére une exception
        // throw $this->createNotFoundException('Page perdue ou inexistante!');
        return $this->render('mentions/cgucgv.html.twig');
    }
    /**
     * @Route("/cookie", name="cookie")
     */
    public function cookie(): Response
    {
        return $this->render('base.html.twig');
    }
}
