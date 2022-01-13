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
class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(CartService $cartService)
    {
        $panierWithData = $cartService->getFullCart();
        $total = $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()

        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, CartService $cartService)
    {

        $cartService->add($id);
        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);
        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(CartService $cartService ): Response
    {
        $commande = $cartService->getTotal();
        \Stripe\Stripe::setApiKey('sk_test_51K6vbcEZDVyiBY3Z3G7d0fYvGF4aH8nXJAlQRaOpvClbtNt18kPPAdQloWAliIrYbCHdBXgmTGKMllsZNW0CNFs000nevPwgwY');
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [[
              'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                  'name' => 'Récaputilatif de votre commande chez SENESafari',
                ],
                'unit_amount' =>  $commande*100,
              ],
              'quantity' => 1,
            ]
        ],
            
        'mode'                 => 'payment',
        'success_url'          => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        'cancel_url'           => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
          ]);
     
        return $this->redirect($session->url,303);
        
    }

    /**
     * @Route("/succes-url", name="success_url")
     */
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
