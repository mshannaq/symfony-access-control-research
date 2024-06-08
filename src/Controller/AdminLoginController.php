<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminLoginController extends AbstractController
{
    #[Route('/admin/login', name: 'app_admin_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {

        // Check if the user is already logged in
        if ($this->getUser()) {
            // Check if the user is an admin
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                //@TODO you have to define the backend_dashboard route or you will get error
                return $this->redirectToRoute('app_backend');
            }
            // If not an admin, redirect to the main route
            //@TODO you have to define the site_index route or you will get error
            return $this->redirectToRoute('site_index');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('admin_login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);


    }


    #[Route('/admin/logout', name: 'app_admin_logout')]
    public function logoutAction()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}