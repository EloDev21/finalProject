<?php

namespace App\Service\Cart;

use App\Repository\CircuitsRepository;
use App\Repository\TripsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartService
{

    protected $session;
    protected $tripsRepository;

    public function __construct(SessionInterface $session, CircuitsRepository $circuitsRepository)
    {
        $this->session = $session;
        $this->circuitsRepository = $circuitsRepository;
    }

    public function add(int $id)
    {
        $panier = $this->session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {

            $panier[$id] = 1;
        }
        $this->session->set('panier', $panier);
    }
    public function remove(int $id)
    {

        $panier = $this->session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]--;
            if ($panier[$id] == 0) {
                unset($panier[$id]);
            }
        }
        $this->session->set('panier', $panier);
    }
    public function getFullCart(): array
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'circuit' => $this->circuitsRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $panierWithData;
    }
    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getFullCart() as $item) {

            $total += $item['circuit']->getPrice() * $item['quantity'];
        }
        return $total;
    }
}
