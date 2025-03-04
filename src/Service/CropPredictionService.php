<?php

namespace App\Service;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CropPredictionService
{
    private string $pythonScriptPath;
    
    public function __construct(string $projectDir)
    {
        $this->pythonScriptPath = $projectDir . '/python-api/app.py';
    }
    
    public function predictCrop(array $features): string
    {
        $process = new Process([
            'C:\\Users\\anisj\\PI3\\venv\\Scripts\\python.exe', // Chemin vers python dans l'environnement virtuel
            $this->pythonScriptPath,
            json_encode($features)
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return trim($process->getOutput());
    }
}
