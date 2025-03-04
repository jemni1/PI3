<?php

namespace App\Controller;

use App\Entity\RecyclageDechet;
use App\Repository\RecyclageDechetRepository;
use App\Services\ReportGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    private ReportGenerator $reportGenerator;

    public function __construct(ReportGenerator $reportGenerator)
    {
        $this->reportGenerator = $reportGenerator;
    }
    #[Route('/report/signature/{id}', name: 'signature_form')]
    public function signatureForm($id, RecyclageDechetRepository $recyclageRepository): Response
    {
        $recyclage = $recyclageRepository->find($id);
        if (!$recyclage) {
            throw $this->createNotFoundException('Recyclage non trouvé.');
        }
    
        return $this->render('report/signature.html.twig', [
            'recyclage' => $recyclage,
            'signature' => null, // Ajout de la variable signature
        ]);
    }
    

    #[Route('/report/generate/{id}', name: 'generate_report', methods: ['POST'])]
    public function generateReport(Request $request, $id, RecyclageDechetRepository $recyclageRepository): Response
    {
        $recyclage = $recyclageRepository->find($id);
        if (!$recyclage) {
            throw $this->createNotFoundException('Recyclage non trouvé.');
        }
    
        // Récupérer la signature encodée en Base64
        $signature = $request->request->get('signature');
    
        if (!$signature) {
            throw new \Exception('Signature manquante.');
        }
    
        // Générer le HTML avec la signature
        $html = $this->renderView('report/signature.html.twig', [
            'recyclage' => $recyclage,
            'signature' => $signature // On passe la signature au template
        ]);
    
        // Générer le PDF avec Snappy
        $pdfContent = $this->reportGenerator->generatePdf('report/signature.html.twig', [
            'recyclage' => $recyclage,
            'signature' => $signature,
            'pdf_mode' => true, // Activer le mode PDF pour masquer les boutons
        ]);
        

    
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="recycling_report.pdf"'
        ]);
    }
    
    
}
