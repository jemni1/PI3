<?php
// src/Controller/CropPredictionController.php
namespace App\Controller;

use App\Form\CropPredictionType;
use App\Service\CropPredictionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CropPredictionController extends AbstractController
{
    #[Route('/predict', name: 'predict_crop', methods: ['GET', 'POST'])]
    public function predict(Request $request, CropPredictionService $predictionService): Response
    {
        $form = $this->createForm(CropPredictionType::class);
        $form->handleRequest($request);
        
        $prediction = null;
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            try {
                $features = [
                    $data['nitrogen'],
                    $data['phosphorus'],
                    $data['potassium'],
                    $data['temperature'],
                    $data['humidity'],
                    $data['ph'],
                    $data['rainfall']
                ];
                
                $prediction = $predictionService->predictCrop($features);
                
                $this->addFlash('success', 'Prediction generated successfully!');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred during prediction: ' . $e->getMessage());
            }
        }
        
        return $this->render('terrain/prediction.html.twig', [
            'form' => $form->createView(),
            'prediction' => $prediction
        ]);
    }
}