<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // Create a new user instance
        $user = new User();

        // Create the form for registration
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the raw password from the form
            $plainPassword = $user->getPassword();

            // Debugging: Check if password is set correctly
            if (empty($plainPassword)) {
                throw new \Exception('Password is empty!');
            }

            // Hash the password before saving
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            // Set the hashed password to the user object
            $user->setPassword($hashedPassword);

            // Persist the user entity
            $userRepository->save($user, true);

            // Redirect or show a success message
            return $this->redirectToRoute('app_login'); // Adjust this route as needed
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
