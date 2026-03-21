<?php

declare(strict_types=1);

namespace App\Controller\api\v1;

use App\Entity\Race;
use App\Repository\RaceRepository;
use App\Entity\CharacterClass;
use App\Repository\CharacterClassRepository;
use App\Entity\Skill;
use App\Repository\SkillRepository;
use App\Entity\Character;
use App\Repository\CharacterRepository;
use App\Entity\Party;
use App\Repository\PartyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1', name: 'api_v1')]
final class ApiController extends AbstractController
{
    #[Route('/races', name: 'api_v1_races_index', methods: ['GET'])]
    public function races(RaceRepository $raceRepository): Response
    {
        $races = $raceRepository->findBy([], ['name' => 'ASC']);
        $data = [];
        foreach ($races as $race) {
            $data[] = [
                'id' => $race->getId(),
                'name' => $race->getName(),
                'description' => $race->getDescription(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/races/{id}', name: 'api_v1_races_show', methods: ['GET'])]
    public function race(Race $race): Response
    {
        $data = [
            'id' => $race->getId(),
            'name' => $race->getName(),
            'description' => $race->getDescription(),
        ];

        return $this->json($data);
    }

    #[Route('/classes', name: 'api_v1_classes_index', methods: ['GET'])]
    public function classes(CharacterClassRepository $characterClassRepository): Response
    {
        $classes = $characterClassRepository->findBy([], ['name' => 'ASC']);
        $data = [];
        foreach ($classes as $class) {
            $data[] = [
                'id' => $class->getId(),
                'name' => $class->getName(),
                'description' => $class->getDescription(),
                'healthDice' => $class->getHealthDice(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/classes/{id}', name: 'api_v1_classes_show', methods: ['GET'])]
    public function class(CharacterClass $characterClass): Response
    {
        // Transforme la collection Doctrine en tableau JSON simple pour l'API
        $skillsData = [];
        foreach ($characterClass->getSkills() as $skill) {
            $skillsData[] = [
                'id' => $skill->getId(),
                'name' => $skill->getName(),
                'ability' => $skill->getAbility(),
            ];
        }

        $data = [
            'id' => $characterClass->getId(),
            'name' => $characterClass->getName(),
            'description' => $characterClass->getDescription(),
            'healthDice' => $characterClass->getHealthDice(),
            'skills' => $skillsData,
        ];

        return $this->json($data);
    }

    #[Route('/skills', name: 'api_v1_skills_index', methods: ['GET'])]
    public function skills(SkillRepository $skillRepository): Response
    {
        $skills = $skillRepository->findBy([], ['name' => 'ASC']);
        $data = [];
        foreach ($skills as $skill) {
            $data[] = [
                'id' => $skill->getId(),
                'name' => $skill->getName(),
                'ability' => $skill->getAbility(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/skills/{id}', name: 'api_v1_skills_show', methods: ['GET'])]
    public function skill(Skill $skill): Response
    {
        $data = [
                'id' => $skill->getId(),
                'name' => $skill->getName(),
                'ability' => $skill->getAbility(),
            ];
        return $this->json($data);
    }

    #[Route('/characters', name: 'api_v1_characters_index', methods: ['GET'])]
    public function characters(Request $request, CharacterRepository $characterRepository): Response
    {
        // Filtres GET publics pour retrouver les personnages sans authentification
        $name = trim((string) $request->query->get('name', ''));
        $classId = (int) $request->query->get('class_id', 0);
        $raceId = (int) $request->query->get('race_id', 0);

        $characters = $characterRepository->findForApi(
            $name !== '' ? $name : null,
            $classId > 0 ? $classId : null,
            $raceId > 0 ? $raceId : null,
        );

        // La liste reste legere pour eviter de dupliquer toutes les statistiques partout
        $data = [];
        foreach ($characters as $character) {
            $data[] = [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'image' => $character->getImage()
                    ? '/uploads/characters/' . $character->getImage()
                    : null,
                'level' => $character->getLevel(),
                'class' => [
                    'id' => $character->getCharacterClass()?->getId(),
                    'name' => $character->getCharacterClass()?->getName(),
                ],
                'race' => [
                    'id' => $character->getRace()?->getId(),
                    'name' => $character->getRace()?->getName(),
                ],
            ];
        }
        return $this->json($data);
    }

    #[Route('/characters/{id}', name: 'api_v1_characters_show', methods: ['GET'])]
    public function character(Character $character): Response
    {
        // Le detail expose les relations utiles au front sans renvoyer les entites Doctrine brutes
        $partiesData = [];
        foreach ($character->getParties() as $party) {
            $partiesData[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
            ];
        }
        $data = [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'image' => $character->getImage()
                    ? '/uploads/characters/' . $character->getImage()
                    : null,
                'level' => $character->getLevel(),
                'strength' => $character->getStrength(),
                'dexterity' => $character->getDexterity(),
                'constitution' => $character->getConstitution(),
                'intelligence' => $character->getIntelligence(),
                'wisdom' => $character->getWisdom(),
                'charisma' => $character->getCharisma(),
                'healthPoints' => $character->getHealthPoints(),
                'class' => [
                    'id' => $character->getCharacterClass()?->getId(),
                    'name' => $character->getCharacterClass()?->getName(),
                ],
                'race' => [
                    'id' => $character->getRace()?->getId(),
                    'name' => $character->getRace()?->getName(),
                ],
                'parties' => $partiesData,
            ];
        return $this->json($data);
    }

    #[Route('/parties', name: 'api_v1_parties_index', methods: ['GET'])]
    public function parties(Request $request, PartyRepository $partyRepository): Response
    {
        // Filtre GET public sur l'etat de remplissage du groupe
        $status = (string) $request->query->get('status', '');
        $status = in_array($status, ['full', 'available'], true) ? $status : null;

        $parties = $partyRepository->findFilteredByAvailability($status);

        $data = [];
        foreach ($parties as $party) {
            $data[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'description' => $party->getDescription(),
                'maxSize' => $party->getMaxSize(),
                'memberCount' => $party->getCharacters()->count(),
                'creator' => [
                    'id' => $party->getUser()?->getId(),
                    'username' => $party->getUser()?->getUsername(),
                ],
            ];
        }

        return $this->json($data);
    }

    #[Route('/parties/{id}', name: 'api_v1_parties_show', methods: ['GET'])]
    public function party(Party $party): Response
    {
        // Le detail du groupe embarque ses membres avec les infos utiles au front
        $membersData = [];
        foreach ($party->getCharacters() as $character) {
            $membersData[] = [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'level' => $character->getLevel(),
                'class' => [
                    'id' => $character->getCharacterClass()?->getId(),
                    'name' => $character->getCharacterClass()?->getName(),
                ],
                'race' => [
                    'id' => $character->getRace()?->getId(),
                    'name' => $character->getRace()?->getName(),
                ],
            ];
        }

        $data = [
            'id' => $party->getId(),
            'name' => $party->getName(),
            'description' => $party->getDescription(),
            'maxSize' => $party->getMaxSize(),
            'memberCount' => $party->getCharacters()->count(),
            'creator' => [
                'id' => $party->getUser()?->getId(),
                'username' => $party->getUser()?->getUsername(),
            ],
            'members' => $membersData,
        ];

        return $this->json($data);
    }
}
