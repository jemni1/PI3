<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Psr\Log\LoggerInterface;

class MailerService
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    public function sendEmail(Email $email, string $subject, string $content, bool $isHtml = false): bool
    {
        try {
            $this->logger->info('MailerService: Starting email send process', [
                'to' => $email->getTo()[0]->getAddress(), // Log the recipient
                'subject' => $subject
            ]);

            // Set subject and content on the provided Email object
            $email->subject($subject);
            if ($isHtml) {
                $email->html($content);
            } else {
                $email->text($content);
            }

            $this->logger->info('MailerService: Email object prepared successfully');

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