<?php
// src/Service/YoloDetectionService.php
namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class YoloDetectionService
{
    public function detectWeedsAndCrops($imagePath)
{
    // Spécifie le chemin du script Python et de l'image
    $process = new Process(['python', __DIR__.'/../../scripts/yolo_detect.py', $imagePath]);
    $process->run();

    // Si le processus échoue, lever une exception avec la sortie d'erreur
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    // Traiter la sortie du script Python
    $output = $process->getOutput();
    
    // Vérifier si la sortie est vide
    if (empty($output)) {
        throw new \Exception("Aucune détection n'a été effectuée ou une erreur est survenue dans le script Python.");
    }

    // Retourner les résultats (au format JSON ou autre)
    return json_decode($output, true);  // Si ton script retourne du JSON
}

}
