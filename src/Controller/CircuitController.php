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

      $circuit = new Circuits;
 
        $formCircuit = $this->createForm(CircuitType::class, $circuit);
        $formCircuit->handleRequest($request);
        if ($formCircuit->isSubmitted() && $formCircuit->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($circuit);
            $em->flush();
            $this->addFlash('message', 'Le circuit a été ajouté avec succès! ');
            return $this->redirectToRoute('circuit');
        }
        return $this->render('circuit/add.html.twig', [
            'formCircuit' => $formCircuit->createView()
        ]);
    }
}
