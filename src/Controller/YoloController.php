<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;

class YoloController extends AbstractController
{
    
    #[Route('/detection', name: 'detection')]
    public function index(Request $request): Response
    {
        $output = null;
        // Chemin vers le modèle YOLOv8
        $modelPath = "../scripts/best.pt";
        // Chemin complet vers le script Python
        $scriptPath = $this->getParameter('kernel.project_dir') 
        . DIRECTORY_SEPARATOR . "scripts" 
        . DIRECTORY_SEPARATOR . "detect.py";
        // Vérification de l'existence du modèle
        if (!file_exists($modelPath)) {
            throw new \Exception("Le fichier best.pt n'existe pas !");
        }

        if ($request->isMethod('POST')) {
            // Récupération du fichier uploadé par le client via le champ 'image'
            $uploadedFile = $request->files->get('image');
            if ($uploadedFile) {
                // Définir le répertoire de destination pour l'image uploadée
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                // Générer un nom unique pour l'image afin d'éviter les conflits
                $newFilename = uniqid() . '.' . $uploadedFile->guessExtension();
                try {
                    $uploadedFile->move($uploadDir, $newFilename);
                } catch (FileException $e) {
                    throw new \Exception("Erreur lors de l'upload de l'image : " . $e->getMessage());
                }
                // Chemin complet de l'image uploadée
                $imagePath = $uploadDir . '/' . $newFilename;

                // Exécuter le script Python en passant le chemin du modèle et de l'image uploadée
                $process = new Process(['python', $scriptPath, $modelPath, $imagePath]);
                $process->run();

                // En cas d'erreur lors de l'exécution du script
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }

                // Récupérer la sortie du script (résultats de la détection)
                $output = $process->getOutput();
            }
        }

        // Rendre la vue en passant la variable 'output' (résultats de la détection, s'il y en a)
        return $this->render('yolo/index.html.twig', [
            'output' => $output,
        ]);
    }
}
