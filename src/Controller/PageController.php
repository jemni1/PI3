<?php
// src/Controller/PageController.php
namespace App\Controller;

use App\Service\OpenAIService;
use App\Form\QuestionFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private OpenAIService $aiService;

    public function __construct(OpenAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    #[Route('/chatbot', name: 'chatbot')]
    public function chatbot(Request $request): Response
    {
        // Créer le formulaire
        $form = $this->createForm(QuestionFormType::class);
        $form->handleRequest($request);

        $response = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $question = $form->get('question')->getData();
            $response = $this->aiService->askAI($question);
        }

        return $this->render('chatbot/chatbot.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'question' => $question ?? null,
        ]);
    }
}
