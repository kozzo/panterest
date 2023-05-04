<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin')]
    // #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas les droits d\'accès à cette page.');

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/pins', name:'app_admin_pins_index')]
    // #[IsGranted('ROLE_ADMIN')]
    public function pinsIndex(): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'Vous n\'avez pas les droits d\'accès à cette page.');

        return $this->render('admin/pin_index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

}
