<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'admin_notifications', methods: ['GET'])]
    public function getNotifications(NotificationRepository $notificationRepository): JsonResponse
    {
        $notifications = $notificationRepository->findUnreadNotifications();
        
        $data = [];
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/mark-as-read/{id}', name: 'mark_notification_read', methods: ['POST'])]
    public function markAsRead(int $id, NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): Response
    {
        $notification = $notificationRepository->find($id);
        
        if ($notification) {
            $notification->setIsRead(true);
            $entityManager->flush();
        }

        return new Response('OK');
    }
}
