<?php

namespace App\Controller;

use App\Entity\Character;
use App\Repository\CharacterClassRepository;
use App\Repository\CharacterRepository;
use App\Repository\PartyRepository;
use App\Repository\RaceRepository;
use App\Repository\SkillRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DisplayController extends AbstractController
{
    public function __construct(
        private readonly SkillRepository $skillRepository,
        private readonly UserRepository $userRepository,
        private readonly RaceRepository $raceRepository,
        private readonly CharacterClassRepository $characterClassRepository,
    )
    {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): Response
    {
        $user = $this->getUser();
        $admin = $user && in_array('ROLE_ADMIN', $user->getRoles(), true);

        return $this->render('pages/home.html.twig', [
            'userCount' => $this->userRepository->count([]),
            'skillCount' => $this->skillRepository->count([]),
            'raceCount' => $this->raceRepository->count([]),
            'classCount' => $this->characterClassRepository->count([]),
            'admin' => $admin,
        ]);
    }


    #[Route('/users', name: 'app_user_index', methods: ['GET'])]
    // La liste complete des utilisateurs est accessible uniquement aux administrateurs.
    #[IsGranted('ROLE_ADMIN')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('pages/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
