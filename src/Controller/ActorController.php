<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActorRepository $actorRepository): Response 
    {
        $actor= new Actor();
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actorRepository->save($actor, true);

            return $this->redirectToRoute('actor_show', ['id' => $actor->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actor/new.html.twig', [
            'actor' => $actor,
            'form' => $form
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Actor $actor, Request $request, ActorRepository $actorRepository): Response 
    {
        $form = $this->createForm(ActorType::class, $actor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actorRepository->save($actor, true);

            return $this->redirectToRoute('actor_show', ['id' => $actor->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('actor/edit.html.twig', [
            'actor' => $actor,
            'form' => $form
        ]);
    }



    #[Route('/{id}', name: 'show')]
    // #[Entity('actor', options: ['mapping' => ['id' => 'id']])]
    public function show(Actor $actor, Request $request, ActorRepository $actorRepository): Response //param converter con la id
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor with id : ' . $actor->getId() . ' found in actor\'s table.'
            );
        }
        // $actor = new Actor();
        // $form = $this->createForm(ActorType::class, $actor);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $actorRepository->save($actor, true);

        //     return $this->redirectToRoute('actor_show', ['id' => $actor->getId()], Response::HTTP_SEE_OTHER);
        // }



        return $this->render('actor/show.html.twig', ['actor' => $actor,]);
    }

    
}

