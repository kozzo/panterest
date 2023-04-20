<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Repository\PinRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PinType;


class PinsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(PinRepository $pinRepository): Response
    {
        $pins = $pinRepository->findBy([],['createdAt' => 'DESC']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    #[Route('/pins/{id<[0-9]+>}', name:'app_pins_show')]
    public function show(Pin $pin): Response{
        return $this->render('pins/show.html.twig', compact('pin'));
    }

    #[Route('/pins/create', name:'app_pins_create', methods:['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response{
        $pin = new Pin;

        $form = $this   ->createForm(Pintype::class, $pin);
                        // ->add('title', TextType::class)
                        // ->add('description', TextareaType::class)
                        // ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            // $data = $form->getData();
            // $pin = new Pin;
            // $pin->setTitle($data['title']);
            // $pin->setDescription($data['description']);
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Pin créé avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/pins/{id<[0-9]+>}/edit', name:'app_pins_edit', methods:['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response{

        $form = $this   ->createForm(Pintype::class, $pin, ['method' => 'PUT']);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em->flush();
            $this->addFlash('info', 'Pin modifié avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/edit.html.twig', ['pin'=>$pin, 'form'=>$form->createView()]);
    }

    #[Route('/pins/{id<[0-9]+>}/delete', name:'app_pins_delete', methods:['DELETE'])]
    public function delete(Pin $pin, Request $request, EntityManagerInterface $em): Response{
        
        if($this->isCsrfTokenValid('pins.delete'.$pin->getId(), $request->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();

            $this->addFlash('error', 'Pin supprimé avec succès !');
        }

        return $this->redirectToRoute('app_home');
    }
}
