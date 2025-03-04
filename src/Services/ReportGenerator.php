<?php
namespace App\Services;

use Knp\Snappy\Pdf;
use Twig\Environment;

class ReportGenerator
{
    private Pdf $snappy;
    private Environment $twig;

    public function __construct(Pdf $snappy, Environment $twig)
    {
        $this->snappy = $snappy;
        $this->twig = $twig;
    }

    public function generatePdf(string $template, array $data): string
    {
        $html = $this->twig->render($template, $data);
        return $this->snappy->getOutputFromHtml($html); // Utilisation correcte
    }
}
