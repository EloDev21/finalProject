<?php

namespace App\Controller;

use App\Repository\OrderRepository;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DomPdfController extends AbstractController
{
    /**
     * @Route("/facture", name="dom_pdf")
     */
    public function index(): Response
    {
    
             $orderRepo = $this->getDoctrine()->getRepository(OrderRepository::class);
            
            $options = new Options();
            $options->set('defaultFont', 'Arial');
    
            $dompdf = new Dompdf($options);
    
            $html = $this->renderView('dom_pdf/index.html.twig', [
                'facture'=> $orderRepo
            ]);
    
            $dompdf->loadHtml($html);
    
            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
    
            // Render the HTML as PDF
            $dompdf->render();
    
            // Output the generated PDF to Browser
            $dompdf->stream("Facture SENE'Safari -", [
                "Attachment" => true
            ] );
          
    
            $this->render('success/success.html.twig', [
                'facture' => $orderRepo
            ]);
        }
    
    
}
