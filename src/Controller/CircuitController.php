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
        // on cree une variable $circuits puis on utilise Doctrine pour acceder à notre BDD 
        // plus précisèment à la table circuits à laquelle on applique la requete SQL findAll()
        // pour afficher tous les elements présents dans notre table circuit
        // on envoie $circuits à la vue index du dossier circuit
        // on voit le parametre circuits à notre vue
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
    // on cree une variable $circuit puis on utilise Doctrine pour acceder à notre BDD 
        // plus précisèment à la table circuits à laquelle on applique la requete SQL find($id)
        // pour acceder à un element précis
        $circuit = $this->getDoctrine()->getRepository(Circuits::class)->find($id);
          // form type (Cart) qui  décrit les champs de formulaire liés à un modèle (modele cart). 
        $formCircuit = $this->createForm(CircuitType::class, $circuit);
        // on cree une requete grace à la librairie Request de HttpFoundation pour avoir acces au formulaire 
        $formCircuit->handleRequest($request);
           // si le formulaire est soumis et est valide
        if ($formCircuit->isSubmitted() && $formCircuit->isValid()) {
            // on appelle l'entity manager de Doctrine pour pouvoir ecrire des donnees en base de donnee
            $em = $this->getDoctrine()->getManager();
            // on enregistre le  circuit modifié (pas besoin d'un persist pour la modification de données)
            $em->flush();
            // on rajoute un message flash pour informer que le circuit est bien modifié et on redirige vers la route avec le nom circuit
            $this->addFlash('message', 'Le circuit a été modifié avec succès!!! ');
            return $this->redirectToRoute('circuit');
        }
        // on renvoie en param le formulaire et on crée une  vue pour utiliser toutes les valeurs 
        return $this->render('circuit/edit.html.twig', [
            'formCircuit' => $formCircuit->createView()
        ]);
    }
    /**
     * @Route("/circuit/delete/{id}", name="circuit_delete")
     */
    public function delete($id)
    {
       // on cree une variable $circuit puis on utilise Doctrine pour acceder à notre BDD 
        // plus précisèment à la table circuits à laquelle on applique la requete SQL find($id)
        // pour acceder à un element précis
        $circuit = $this->getDoctrine()->getRepository(Circuits::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        // on attribue l'id de notre élément séléctionné à la fonction remove
        $em->remove($circuit);
        // on enregistre les modifications en BDD
        $em->flush();
         // on rajoute un message flash pour informer que le circuit eszt bien modifié et on redirige vers la route avec le nom circuit
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
        // si le formulaire est valide est est soumis
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
