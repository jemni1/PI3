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

    public function sendEmail(string $to, string $subject, string $content): bool
    {
        try {
            $this->logger->info('MailerService: Starting email send process', [
                'to' => $to,
                'subject' => $subject
            ]);

            $email = (new Email())
                ->from('monta.bellakhal10@gmail.com')
                ->to($to)
                ->subject($subject)
                ->text($content);

            $this->logger->info('MailerService: Email object created successfully');

            $this->mailer->send($email);

            $this->logger->info('MailerService: Email sent successfully', [
                'to' => $to,
                'subject' => $subject
            ]);

            return true;

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('MailerService: Transport exception', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'to' => $to,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('MailerService: Unexpected error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'to' => $to,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}