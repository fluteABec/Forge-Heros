<?php

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Party;
use App\Entity\User;
use App\Form\PartyType;
use App\Repository\PartyRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/party')]
final class PartyController extends AbstractController
{
    #[Route(name: 'app_party_index', methods: ['GET'])]
    public function index(Request $request, PartyRepository $partyRepository): Response
    {
        // Securite cote serveur -> seuls les utilisateurs connectes voient les groupes
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Filtre GET pour distinguer les groupes complets et ceux avec de la place
        $status = $request->query->getString('status', '');
        $status = in_array($status, ['full', 'available'], true) ? $status : null;

        return $this->render('party/index.html.twig', [
            'parties' => $partyRepository->findFilteredByAvailability($status),
            'filters' => [
                'status' => $status,
            ],
        ]);
    }

    #[Route('/new', name: 'app_party_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Securite cote serveur -> non connecte -> pas d'acces a la creation
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $party = new Party();
        $form = $this->createForm(PartyType::class, $party, [
            'user' => $this->getUser(),
            'party' => $party,
            'is_owner' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous devez etre connecte pour creer un groupe.');
            }

            $party->setUser($user);

            // Synchronise uniquement les personnages choisis par l'utilisateur courant
            $this->syncUserCharacters($party, $user, $form->get('characters')->getData());

            // Validation supplementaire pour bloquer les incoherences de capacite
            if ($this->addValidationErrors($form, $validator->validate($party))) {
                return $this->render('party/new.html.twig', [
                    'party' => $party,
                    'form' => $form,
                ]);
            }

            $entityManager->persist($party);
            $entityManager->flush();

            return $this->redirectToRoute('app_party_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('party/new.html.twig', [
            'party' => $party,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_party_show', methods: ['GET'])]
    public function show(Party $party): Response
    {
        // Securite cote serveur -> non connecte -> pas d'acces au detail
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('party/show.html.twig', [
            'party' => $party,
            'is_owner' => $this->getUser() === $party->getUser(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_party_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Party $party, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Securite cote serveur -> non connecte -> pas d'acces a la modification
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Vous devez etre connecte pour modifier un groupe.');
        }

        // Le createur peut tout modifier, les autres seulement leurs personnages dans le groupe
        $isOwner = $user === $party->getUser();
        $form = $this->createForm(PartyType::class, $party, [
            'user' => $user,
            'party' => $party,
            'is_owner' => $isOwner,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->syncUserCharacters($party, $user, $form->get('characters')->getData());

            if ($this->addValidationErrors($form, $validator->validate($party))) {
                return $this->render('party/edit.html.twig', [
                    'party' => $party,
                    'form' => $form,
                    'is_owner' => $isOwner,
                ]);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_party_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('party/edit.html.twig', [
            'party' => $party,
            'form' => $form,
            'is_owner' => $isOwner,
        ]);
    }

    #[Route('/{id}', name: 'app_party_delete', methods: ['POST'])]
    public function delete(Request $request, Party $party, EntityManagerInterface $entityManager): Response
    {
        // Securite cote serveur -> suppression reservee au createur du groupe
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->getUser() !== $party->getUser()) {
            throw $this->createAccessDeniedException('Seul le createur du groupe peut le supprimer.');
        }

        if ($this->isCsrfTokenValid('delete'.$party->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($party);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_party_index', [], Response::HTTP_SEE_OTHER);
    }

    private function syncUserCharacters(Party $party, User $user, mixed $selectedCharacters): void
    {
        $selectedCharacters = $selectedCharacters instanceof Collection ? $selectedCharacters : null;

        // Retire uniquement les personnages du joueur courant qu'il a decoches
        foreach ($party->getCharacters()->toArray() as $character) {
            if ($character->getUser() === $user && (null === $selectedCharacters || !$selectedCharacters->contains($character))) {
                $party->removeCharacter($character);
            }
        }

        if (null === $selectedCharacters) {
            return;
        }

        // Ajoute uniquement les personnages du joueur courant qu'il a coches
        foreach ($selectedCharacters as $character) {
            if ($character instanceof Character && $character->getUser() === $user) {
                $party->addCharacter($character);
            }
        }
    }

    private function addValidationErrors($form, iterable $violations): bool
    {
        $hasErrors = false;

        // Reinjecte les erreurs metier dans le formulaire pour rester sur l'ecran d'edition
        foreach ($violations as $violation) {
            $field = (string) $violation->getPropertyPath();

            if ($field !== '' && $form->has($field)) {
                $form->get($field)->addError(new FormError($violation->getMessage()));
            } else {
                $form->addError(new FormError($violation->getMessage()));
            }

            $hasErrors = true;
        }

        return $hasErrors;
    }
}
