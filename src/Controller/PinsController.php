<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;


class PinsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods:['GET'])]
    public function index(  Request $request,
                            PinRepository $pinRepository,
                            PaginatorInterface $paginator): Response
    {
        $pins = $pinRepository->findBy([],['createdAt' => 'DESC']);
        
        //On compte le nombre de pins
        $number_pins = count($pins);
        
        //On utilise la fonction paginate de KNP Paginator pour définir le nombre de pins par page
        $limit = $paginator->paginate($pins, $request->query->getInt('page', 1), 9);

        //À l'affichage de la vue, on utilise la limite (9 par page) et le nombre de pins
        return $this->render('pins/index.html.twig', ['pins'=>$limit, 'number_pins'=>$number_pins]);
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
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Pin créé avec succès !');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('pins/create.html.twig', ['form'=>$form->createView()]);
    }

    #[Route('/pins/{id<[0-9]+>}/edit', name:'app_pins_edit', methods:['GET', 'PUT'])]
    #[Security("is_granted('PIN_MANAGE', pin)")]
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
    #[Security("is_granted('PIN_MANAGE', pin)")]
    public function delete(Pin $pin, Request $request, EntityManagerInterface $em): Response{
        
        if($this->isCsrfTokenValid('pins.delete'.$pin->getId(), $request->request->get('csrf_token'))){
            $em->remove($pin);
            $em->flush();

            $this->addFlash('error', 'Pin supprimé avec succès !');
        }

        return $this->redirectToRoute('app_home');
    }
}
