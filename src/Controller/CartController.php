<?php

namespace App\Controller;

use App\Entity\Cart;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Order;
use App\Service\Cart\CartService;
use App\Entity\Circuits;
use App\Entity\User;
use App\Form\CartType;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use SendGrid\Mail\Attachment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(CartService $cartService, Request $request)
    
    {
        // on utilise le service CartService pour acceder aux methodes definies dedans
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            //redirection vers la page index du dossier cart
            // envoi de deux parametres à la vue et recuperer le contenu de notre panier ainsi que son total

        ]);
    }
    /**
     * @Route("/facture", name="facture")
     */
    public function facture(CartRepository $cartrepo, CartService $cartService)
    {
        // on recupere l'utilisateur actuellement connecté
        $user = $this->getUser();
        $orders = $cartrepo->findAll();
        //on crée un objet de type Options (librairie domPdf) 
        $options = new Options();
        // on lui affecte à cet objet une police par défaut
        $options->set('defaultFont', 'Arial');
        // on instacie un objet de type DomPdf(librarie domPdf) et on lui donne en param l'objet créé précedemment avec la police
        $dompdf = new Dompdf($options);
        // on redirige vers la page facture du dossier cart et on envoie les parametres (items,total,orders,user) à la vue 
        // pour pouvoir les afficher 
        $html = $this->renderView('cart/facture.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'orders' => $orders,
            'user' => $user
        ]);
// on recupere le fichier grace à la methode loadHtml
        $dompdf->loadHtml($html);

        // (facultatif)gere la taille du document  ainsi que son orientation 
        $dompdf->setPaper('A4', 'portrait');

        // recupere la page html en pdf
        $dompdf->render();

        // on genere le pdf grace à la fonction stream qui reçoit en parametre le titre de la facture et un tableau 
        // "Attachment" => true nous permettra de pouvoir download la facture contrairement à false ou elle est s'affiche
        $dompdf->stream("Votre facture chez SENE'Safari", [
            "Attachment" => true
        ] );
    }
    /**
     * @Route("/confirm", name="confirm_checkout")
     */
    public function prePayment(CartService $cartService, Request $request)

    {
        // on instancie un objet de type cart
        $cart = new Cart();
        // on cree un formulaire grace au FormBundle de symfony et on lui donne en parametre le 
        // form type (Cart) qui  décrit les champs de formulaire liés à un modèle (modele cart). 
        $form = $this->createForm(CartType::class, $cart);
        // on cree une requete grace à la librairie Request de HttpFoundation pour avoir acces au formulaire 
        $form->handleRequest($request);
        // si le formulaire est soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // on appelle l'entity manager de Doctrine pour pouvoir ecrire des donnees en base de donnee
            $em= $this->getDoctrine()->getManager();
             // $cart->setRsv($request->request->get('reservation'));
            // on envoie notre objet cart créé plus haut à notre entity manager
            $em->persist($cart);
            // on sauvegarde l'objet en base de données
            $em->flush();

        }
 
        // redirection vers la page cart_recap du dossier cart  avec les parametres (items,total)
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
        // on appelle le service cartservice et on attribue l'id de notre élément séléctionné a la fonction add
        $cartService->add($id);
        // redirection sur la route avec le nom cart_index
        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, CartService $cartService)
    {
        // on appelle le service cartservice et on attribue l'id de notre élément séléctionné à la fonction remove
        $cartService->remove($id);
        // redirection sur la route avec le nom cart_index
        return $this->redirectToRoute("cart_index");
    }
    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkout(CartService $cartService , \Swift_Mailer $mailer, SessionInterface $session): Response
    {
        // on intialise une variable session  qui sera la session en cours qu'on a pu récuperer grâce à la SessionInterface de symfony
       $this->session= $session;
       // on instancie un objet de type cart
       $cart =new Cart();
    //    on set la valeur de creation du panier
       $cart->setCreatedAt(new \DateTime());
    //    on recupere le total de notre commande 
        $commande = $cartService->getTotal();
        // on recupere l'utilisateur connecté 
        $user = $this->getUser();
        // on met notre clé secrete Stripe pour utiliser le site et simuler le paiement.
        // on définira le prix ,la devise, le titre affiché , unité qu'on multiplie par 100 pour avoir le prix réél ainsi que la quantité
        // le mode de paiement et les redirections en cas d'annulation de  paiement ou de suucès de paiement 
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
        //   on cree un email grace à la librairie SwiftMailer de Symfony
          $recap = (new \Swift_Message('Résérvation contact - SeneSAFARI'))
        //   objet du mai
          ->setSubject(' Récaputilatif de votre commande ')
        //   expediteur
          ->setFrom('contact@senesafari.com')
        //   destinataire en récuperant le mail de notre objet user créé plus haut 
          ->setTo($user->getEmail())
        //   Message contenu dans l'email 
        // redirection vers le fichier mail du dossier cart avec les parametres user et commande
          ->setBody(
              $this->renderView(
              
                  'cart/mail.html.twig',
                  ['user' => $user,
                      'commande' =>$commande,
                       
                  ]
              ),
              'text/html'
          );  
          return $this->redirect($session->url,303);
          $panierWithData = $cartService->getFullCart();
          $em=$this->getDoctrine()->getManager();
          $cart->setFirstname($user->getFirstname());
          $cart->setLastname($user->getLastname());
          $cart->setTotal($commande);
          $cart->setCircuitName('voili voilouuu à modifier');
          $em->persist($cart);
          $em->flush(); 
          $mailer->send($recap);
          $panier = $this->session->get('panier', []);
          unset(  $panier);
          unset(  $panierWithData);
    }

    /**
     * @Route("/success-url", name="success_url")
     */
    public function successUrl(CartService $cartService): Response
    {
    // redirection vers le fichier success du dossier payment avec les parametres items et total 
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
         // redirection vers le fichier mail du dossier payment avec les parametres items et total 
        return $this->render('payment/cancel.html.twig');
    }
}
