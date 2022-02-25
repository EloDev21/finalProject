<?php

namespace App\Controller;
use App\Service\Cart\CartService;
use App\Entity\Circuits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment")
     */
    public function index(CartService $cartService): Response
    {
        $panierWithData = $cartService->getFullCart();
        $total = $cartService->getTotal();
       
        return $this->render('payment/index.html.twig', [
        
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }
    public function successUrl(): Response
    {
        return $this->render('payment/success.html.twig');
    }
    /**
     * @Route("/cancel-url", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('payment/success.html.twig');
    }
}
