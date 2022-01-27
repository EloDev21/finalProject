<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;


class DomPdfController extends AbstractController
{
    #[Route('/dom/pdf/{order_id}', name: 'dom_pdf',  methods: ["GET"])]
    public function domPdf($order_id, CartRepository $cartRepository)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        $html = $this->renderView('dom_pdf/index.html.twig', [
            'facture'=> $cartRepository->findOneBy(['id' => $order_id])
        ]);

        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream("Facture - SENE-SAFARI", [
            "Attachment" => true
        ] );

        $this->render('succes/succes.html.twig', [
            'facture' => $cartRepository->findOneBy(['id' => $order_id])
        ]);
    }
}