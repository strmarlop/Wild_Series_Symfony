<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Actor;



#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/{id}', name: 'show')]
    public function show(Actor $actor): Response //param converter con la id
    {
        if (!$actor) {
            throw $this->createNotFoundException(
                'No actor with id : ' . $actor->getId() . ' found in actor\'s table.'
            );
        }

        return $this->render('actor/show.html.twig', ['actor' => $actor,]);
    }
}

