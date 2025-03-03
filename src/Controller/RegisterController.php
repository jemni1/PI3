<?php 
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        // Retrieve the recaptcha site key from environment
        $recaptchaSiteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? ''; // Ensure this is being loaded correctly

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user, [
            'validation_groups' => ['registration']
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save user to database
            $user->setPassword(
                $passwordHasher->hashPassword($user, $user->getPassword())
            );
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Registration successful! Please log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
            'recaptchaSiteKey' => $recaptchaSiteKey // Pass the site key to the template
        ]);
    }
}