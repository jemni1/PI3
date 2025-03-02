<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

class TwoFactorController extends AbstractController
{
    #[Route('/2fa', name: 'app_2fa')]
    public function index(Request $request, Security $security, FormFactoryInterface $formFactory): Response
    {
        $user = $security->getUser();
        if (!$user instanceof User || !$user->getIsTotpEnabled()) {
            // If the user is not logged in or does not have 2FA enabled, redirect them to login
            return $this->redirectToRoute('app_login');
        }

        // Create the form for entering the 2FA code
        $form = $this->createFormBuilder()
            ->add('totp_code', TextType::class, [
                'label' => 'Enter the 2FA code from your authenticator app',
                'attr' => ['autofocus' => true],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totpCode = $form->getData()['totp_code'];

            // Check if the entered TOTP code is valid (this is where you would integrate with a TOTP service like Google Authenticator)
            if ($this->isTotpCodeValid($user, $totpCode)) {
                // Successfully authenticated via 2FA, you can redirect to the home page or dashboard
                return $this->redirectToRoute('app_home');
            } else {
                // Invalid code, add an error
                $this->addFlash('error', 'The 2FA code you entered is incorrect.');
            }
        }

        return $this->render('two_factor/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function isTotpCodeValid(User $user, string $totpCode): bool
    {
        // This function should check the TOTP code against the user's TOTP secret
        // You can use a library like "scheb/2fa" or another package that supports TOTP
        // Here you would verify the code, for example, by calling:
        // return $this->totpService->verifyCode($user->getTotpSecret(), $totpCode);
        
        // For now, return true for demonstration purposes
        return true;
    }
}
