<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function show(): Response
    {
        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }

    #[Route('/account/edit', name: 'app_account_edit', methods:['GET', 'PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, EntityManagerInterface $em): Response{
        //On récupère les données de User
        $user = $this->getUser();
        //On applique les données dans le formulaire
        $form = $this->createForm(UserFormType::class, $user, ['method'=>'PATCH']);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'Compte modifié avec succès !');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/account/change-password', name: 'app_account_change_password', methods:['GET', 'PATCH'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function changePassword( Request $request,
                                    EntityManagerInterface $em,
                                    UserPasswordHasherInterface $userPasswordHasher): Response{
        
        $user = $this->getUser();
        $form = $this->createForm(  ChangePasswordFormType::class, 
                                    null, 
                                    [
                                        'current_password_is_required'=>true,
                                        'method'=>'PATCH'
                                    ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            ));

            $em->flush();

            $this->addFlash("success", "Mot de passe modifié avec succès !");

            return $this->redirectToRoute('app_account');
        }

        return $this->render("account/change_password.html.twig", [
            'form' =>$form->createView()
        ]);
    }
}