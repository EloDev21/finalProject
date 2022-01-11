<?php

namespace App\Controller;

use App\Entity\Circuits;
use App\Form\CircuitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CircuitController extends AbstractController
{
    /**
     * @Route("/circuit", name="circuit")
     */
    public function index(): Response
    {
        $circuits = $this->getDoctrine()->getRepository(Circuits::class)->findAll();
        return $this->render('circuit/index.html.twig', [
            'circuits' => $circuits
         
            
           
        ]);
    }
        /**
     * @Route("/circuit/edit/{id}", name="circuit_edit")
     */
    public function edit(Request $request, $id)
    {

        $circuit = $this->getDoctrine()->getRepository(Circuits::class)->find($id);
 
        $formCircuit = $this->createForm(CircuitType::class, $circuit);
        $formCircuit->handleRequest($request);
        if ($formCircuit->isSubmitted() && $formCircuit->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $em->flush();
            $this->addFlash('message', 'Le circuit a été modifié avec succès!!! ');
            return $this->redirectToRoute('circuit');
        }
        return $this->render('circuit/edit.html.twig', [
            'formCircuit' => $formCircuit->createView()
        ]);
    }
    /**
     * @Route("/circuit/delete/{id}", name="circuit_delete")
     */
    public function delete($id)
    {
        $circuit = $this->getDoctrine()->getRepository(Circuits::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($circuit);
        $em->flush();
        $this->addFlash('message', 'Circuit supprimé avec succès!!! ');
        return $this->redirectToRoute('circuit');
    }
    /**
     * @Route("/circuit/add", name="circuit_add")
     */
    public function add(Request $request)
    {
        // creation d'un nouveau objet circuit grace à l'entité Circuits de notre base de donnees
      $circuit = new Circuits;
 
    //   creation d'un form grace au formtype importée (circuit type)
        $formCircuit = $this->createForm(CircuitType::class, $circuit);
        // lance la requete grace à httpfoundation Request
        $formCircuit->handleRequest($request);
        // si le circuit est valide est est soumis
        if ($formCircuit->isSubmitted() && $formCircuit->isValid()) {
            // on utilise doctrine pour acceder à l'entité
            $em = $this->getDoctrine()->getManager();
            // on prépare l'ajout
            $em->persist($circuit);
            // on l'ajoute à la base
            $em->flush();

            // on redirige l'utilisateur vers la page circuit avec un message de confirmation de l'ajout du circuit
            $this->addFlash('message', 'Le circuit a été ajouté avec succès! ');
            return $this->redirectToRoute('circuit');
        }
        // sinon on redirige vers le formulaire d'ajout et on récupere notre form pour pouvoir l'utilisier dans la vue twig 
        return $this->render('circuit/add.html.twig', [
            'formCircuit' => $formCircuit->createView()
        ]);
    }
}
