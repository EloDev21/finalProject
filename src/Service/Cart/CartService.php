<?php

namespace App\Service\Cart;

use App\Repository\CircuitsRepository;
use App\Repository\TripsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartService
{

    protected $session;
    // on recupere la sessionInterface de symfony et la table contenant tous les circuits 
    public function __construct(SessionInterface $session, CircuitsRepository $circuitsRepository)
    {
        // on initialise la session
        $this->session = $session;
        // on initialise  circuits
        $this->circuitsRepository = $circuitsRepository;
    }
    public function add(int $id)
    {
        // on cree une variable panier et on l'initialise en tableau vide
        $panier = $this->session->get('panier', []);
        // si le panier n'est pas vide on l'incrémente de 1
        if (!empty($panier[$id])) 
            {
                $panier[$id]++;
            } 
        else
            {
        // sinon l'element est egal à 1
                $panier[$id] = 1;
            }
            // on set le panier de la session par le panier vide qu'on a créé plus haut
             $this->session->set('panier', $panier);
    }
    public function remove(int $id)
    {
        // on récupère le panier de la session en cours
        $panier = $this->session->get('panier', []);
        // si le panier n'est pas vide on decremente de 1
        if (!empty($panier[$id])) {
            $panier[$id]--;
            // si le panier est vide
            if ($panier[$id] == 0) {
                // on supprime le panier grace à la fonction unset
                unset($panier[$id]);
            }
        }
        // on set la nouvelle valeur du panier dans la session
        $this->session->set('panier', $panier);
    }
    public function getFullCart(): array
    {
         // on récupère le panier de la session en cours
        $panier = $this->session->get('panier', []);
        // on cree un  tableau vide
        $panierWithData = [];
        // pour chaque panier on recupere la quantité par rapport à l'id du circuit
        foreach ($panier as $id => $quantity) {
            // on recupere le parametre circuit qui representera le circuit séléctionné ainsi que sa quantité

            $panierWithData[] = [
                'circuit' => $this->circuitsRepository->find($id),
                'quantity' => $quantity
            ];
        }
        // retourne notre panier récuperé
        return $panierWithData;
    }
    public function getTotal(): int
    {
        // nouvelle variable total
        $total = 0;

        foreach ($this->getFullCart() as $item) {
            // pour chaque getFullCart on incrèmente le total du panier par rapport à la valeur d'avant 
            // d'où le "+="
            // le total sera le prix du circuit * quantité 

            $total += $item['circuit']->getPrice() * $item['quantity'];
        }
        // retourne le total 
        return $total;
    }
}
