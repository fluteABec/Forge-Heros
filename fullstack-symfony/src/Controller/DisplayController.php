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
        return $this->render('pages/home.html.twig', [
            'userCount' => $this->userRepository->count([]),
            'skillCount' => $this->skillRepository->count([]),
            'raceCount' => $this->raceRepository->count([]),
            'classCount' => $this->characterClassRepository->count([]),
        ]);
    }

    #[Route('/characters', name: 'app_character_index', methods: ['GET'])]
    public function characters(Request $request): Response
    {
        $search = trim((string) $request->query->get('q', ''));

        $queryBuilder = $this->characterRepository->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC');

        if ($search !== '') {
            $queryBuilder
                ->andWhere('LOWER(c.name) LIKE :search')
                ->setParameter('search', '%'.mb_strtolower($search).'%');
        }

        return $this->render('pages/character/index.html.twig', [
            'characters' => $queryBuilder->getQuery()->getResult(),
            'filters' => [
                'q' => $search,
            ],
        ]);
    }

    #[Route('/characters/{id}', name: 'app_character_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function characterShow(int $id): Response
    {
        $character = $this->characterRepository->find($id);
        if (!$character) {
            throw $this->createNotFoundException('Character not found.');
        }
        \assert($character instanceof Character);

        return $this->render('pages/character/show.html.twig', [
            'character' => $character,
            'abilities' => [
                'STR' => $character->getStrength(),
                'DEX' => $character->getDexterity(),
                'CON' => $character->getConstitution(),
                'INT' => $character->getIntelligence(),
                'WIS' => $character->getWisdom(),
                'CHA' => $character->getCharisma(),
            ],
        ]);
    }

    #[Route('/parties', name: 'app_party_index', methods: ['GET'])]
    public function parties(Request $request): Response
    {
        return $this->render('pages/party/index.html.twig', [
            'parties' => $this->partyRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/parties/{id}', name: 'app_party_show', requirements: ['id' => '\\d+'], methods: ['GET'])]
    public function partyShow(int $id): Response
    {
        $party = $this->partyRepository->find($id);
        if (!$party) {
            throw $this->createNotFoundException('Party not found.');
        }

        return $this->render('pages/party/show.html.twig', [
            'party' => $party,
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
