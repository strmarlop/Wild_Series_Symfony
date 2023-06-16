<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


#[Route('/comment', name: 'comment_', methods: ['POST'])]
class CommentController extends AbstractController
{
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])] //episodio para saber donde esta el comment
    // #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {

        if ($this->getUser() !== $comment->getAuthor()) {
            // If not the owner, throws a 403 Access Denied exception
            throw $this->createAccessDeniedException('Only the owner can edit the program!');
        }


        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);            
        }

        $this->addFlash('danger', 'Oh! One comment has been deleted! :O');

        return $this->redirectToRoute('program_episode_show', [
            'slug_program' => $comment->getEpisode()->getSeason()->getProgram()->getSlug(),
            'season' => $comment->getEpisode()->getSeason()->getId(), 
            'slug_episode' => $comment->getEpisode()->getSlug(), 
        ]);
        //Le pasas la ruta y tienes que ir a buscar lo que hay alli
    }
}
