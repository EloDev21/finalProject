<?php

namespace App\Controller;
// Include Dompdf required namespaces

use App\Entity\Cart;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Order;
use App\Service\Cart\CartService;
use App\Entity\Circuits;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
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
     * @Route("/facture", name="facture")
     */
    public function facture(CartRepository $cartrepo, CartService $cartService)
    {
        $orders = $cartrepo->findAll();
        $user = $this->getUser();


        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        $html = $this->renderView('cart/facture.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'orders' => $orders,
            'user' => $user
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("Votre facture chez SENE'Safari", [
            "Attachment" => true
        ] );
    }
    /**
     * @Route("/confirm", name="confirm_checkout")
     */
    public function prePayment(CartService $cartService)
    {
        $panierWithData = $cartService->getFullCart();
        $total = $cartService->getTotal();
        return $this->render('cart/cart_recap.html.twig', [
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
    public function checkout(CartService $cartService , \Swift_Mailer $mailer): Response
    {
       $cart =new Cart();
       $cart->setCreatedAt(new \DateTime());
        $commande = $cartService->getTotal();
        $user = $this->getUser();
        
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
          
          $recap = (new \Swift_Message('Résérvation contact - SeneSAFARI'))
          ->setSubject(' Récaputilatif de votre commande ')
          ->setFrom('contact@senesafari.com')
          ->setTo($user->getEmail())
          ->setBody(
              $this->renderView(
              
                  'cart/mail.html.twig',
                  ['user' => $user,
                      'commande' =>$commande,
                       
                  ]
              ),
              'text/html'
          );
      
          $items = $cartService->getFullCart();
  
          $panierWithData = [];
          $em=$this->getDoctrine()->getManager();
        $cart->setFirstname($user->getFirstname());
        $cart->setLastname($user->getLastname());
        $cart->setTotal($commande);
         $cart->setCircuitName('voili voilouuu à modifier');
      //   $cart->setCircuitName($commande->getCircuitName());
      //   $cart->setCreatedAt( new \DateTime('now')=);
      $em->persist($cart);
      $em->flush();
        $mailer->send($recap);

          return $this->redirect($session->url,303);
        
    }

    /**
     * @Route("/success-url", name="success_url")
     */
    public function successUrl(CartService $cartService): Response
    {
        $panierWithData = $cartService->getFullCart();
        $total = $cartService->getTotal();
  
        return $this->render('payment/success.html.twig',[
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
        

        
    }
    /**
     * @Route("/cancel-url", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }
}
