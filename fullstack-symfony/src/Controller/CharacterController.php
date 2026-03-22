<?php

namespace App\Controller;

use App\Entity\Character;
use App\Entity\User;
use App\Form\CharacterType;
use App\Repository\CharacterClassRepository;
use App\Repository\CharacterRepository;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/character')]
final class CharacterController extends AbstractController
{
    #[Route(name: 'app_character_index', methods: ['GET'])]
    public function index(
        Request $request,
        CharacterRepository $characterRepository,
        CharacterClassRepository $characterClassRepository,
        RaceRepository $raceRepository,
    ): Response
    {
        // Securite cote serveur -> non connecte -> pas d'acces a la liste
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez etre connecte pour consulter vos personnages.');
        }

        // Filtres GET conserves dans l'URL pour pouvoir raffiner la liste
        $name = trim((string) $request->query->get('character_name', ''));
        $classId = (int) $request->query->get('character_class_id', 0);
        $raceId = (int) $request->query->get('character_race_id', 0);

        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findFilteredByUser(
                $user,
                '' === $name ? null : $name,
                $classId > 0 ? $classId : null,
                $raceId > 0 ? $raceId : null,
            ),
            'character_classes' => $characterClassRepository->findBy([], ['name' => 'ASC']),
            'races' => $raceRepository->findBy([], ['name' => 'ASC']),
            'filters' => [
                'character_name' => $name,
                'character_class_id' => $classId > 0 ? $classId : null,
                'character_race_id' => $raceId > 0 ? $raceId : null,
            ],
        ]);
    }

    #[Route('/new', name: 'app_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Securite cote serveur -> non connecte -> pas d'acces a la creation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous devez etre connecte pour creer un personnage.');
            }

            // Calcul simplifie des points de vie a la creation du personnage
            $constitutionModifier = (int) floor(($character->getConstitution() - 10) / 2);
            $healthDice = $character->getCharacterClass()->getHealthDice();
            $healthPoints = $healthDice + $constitutionModifier;

            $character->setHealthPoints($healthPoints);
            $character->setUser($user);

            // Stockage de l'image dans le dossier public pour un acces direct depuis l'app et l'API
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = strtolower(preg_replace('/[^a-zA-Z0-9_-]/', '-', $originalFilename));
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/characters',
                    $newFilename
                );

                $character->setImage($newFilename);
            }

            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        // Affiche le detail d'un personnage deja resolu par son id dans l'URL.
        return $this->render('character/show.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        // Recharge le meme formulaire que la creation, mais pre-rempli avec les donnees existantes.
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        // La suppression est protegee par token CSRF pour eviter les requetes malicieuses.
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
    }
}
