<?php
// src/Controller/ProgramController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProgramRepository;
use App\Repository\SeasonRepository;

#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();

        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    #[Route('/show/{id<^[0-9]+$>}', name: 'show')]
    public function show(int $id, ProgramRepository $programRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $id]); //id debe escribirse como el campo de la entité Program
        // same as $program = $programRepository->find($id);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : ' . $id . ' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program,]);
    }

    #[Route('/{programId}/seasons/{seasonId}', name: 'season_show')] //no repetir program_season_show, el program_ es genérico
    public function showSeason(int $programId, int $seasonId, ProgramRepository $programRepository, SeasonRepository $seasonRepository): Response
    {
        $program = $programRepository->findOneBy(['id' => $programId]); //id debe escribirse como el campo de la entité Program
        $season = $seasonRepository->findOneBy(['id' => $seasonId]); //id debe escribirse como el campo de la entité Season

        if (!$season) {
            throw $this->createNotFoundException(
                'No program with id : ' . $seasonId . ' found in season\'s table.'
            );
        }

        return $this->render('program/season_show.html.twig', ['program' => $program, 'season' => $season]);
    }
}
