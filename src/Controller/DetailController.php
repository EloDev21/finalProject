<?php

namespace App\Controller;

use App\Entity\Circuits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DetailController extends AbstractController
{
    /**
     * @Route("/detail/{id}", name="detail")
     */
    public function index($id): Response
    {
          // on cree une variable $circuit puis on utilise Doctrine pour acceder à notre BDD 
        // plus précisèment à la table circuits à laquelle on applique la requete SQL find($id)
        // pour acceder à un element précis ( page détail d'un circuit)
        $circuit =$this->getDoctrine()->getRepository(Circuits::class)->find($id);
        return $this->render('detail/index.html.twig', [
            'circuit' => $circuit,
        ]);
    }
}
