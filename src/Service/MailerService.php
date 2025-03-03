<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class MailerService
{
    private $mailer;
    private $logger;
    private $twig;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->twig = $twig; // Twig Environment for rendering templates
    }

    public function sendEmail(Email $email, string $subject, string $template, array $context = []): bool
    {
        try {
            $this->logger->info('MailerService: Starting email send process', [
                'to' => $email->getTo()[0]->getAddress(),
                'subject' => $subject,
                'template' => $template
            ]);

            // Set the subject on the email
            $email->subject($subject);

            // Render the Twig template (e.g., verification_code.html.twig) with the context
            $htmlContent = $this->twig->render($template, $context);
            $email->html($htmlContent);

            $this->logger->info('MailerService: Email object prepared successfully');

            // Send the email
            $this->mailer->send($email);

            $this->logger->info('MailerService: Email sent successfully', [
                'to' => $email->getTo()[0]->getAddress(),
                'subject' => $subject
            ]);

            return true;

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('MailerService: Transport exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'to' => $email->getTo()[0]->getAddress(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            $this->logger->error('MailerService: Twig template error', [
                'error' => $e->getMessage(),
                'template' => $template,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('MailerService: Unexpected error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'to' => $email->getTo()[0]->getAddress(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}