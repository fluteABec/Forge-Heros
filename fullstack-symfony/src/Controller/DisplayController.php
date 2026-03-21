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
    public function users(userRepository $userRepository): Response
    {
        return $this->render('pages/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
