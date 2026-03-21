<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 20,
        notInRangeMessage: 'Le niveau doit etre compris entre {{ min }} et {{ max }}.'
    )]
    private ?int $level = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $strength = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $dexterity = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $constitution = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $intelligence = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $wisdom = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 8,
        max: 15,
        notInRangeMessage: 'Cette caracteristique doit etre comprise entre {{ min }} et {{ max }}.'
    )]
    private ?int $charisma = null;

    #[ORM\Column]
    private ?int $healthPoints = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Race $race = null;

    #[ORM\ManyToOne(inversedBy: 'characters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CharacterClass $characterClass = null;

    /**
     * @var Collection<int, Party>
     */
    #[ORM\ManyToMany(targetEntity: Party::class, inversedBy: 'characters')]
    private Collection $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): static
    {
        $this->strength = $strength;

        return $this;
    }

    public function getDexterity(): ?int
    {
        return $this->dexterity;
    }

    public function setDexterity(int $dexterity): static
    {
        $this->dexterity = $dexterity;

        return $this;
    }

    public function getConstitution(): ?int
    {
        return $this->constitution;
    }

    public function setConstitution(int $constitution): static
    {
        $this->constitution = $constitution;

        return $this;
    }

    public function getIntelligence(): ?int
    {
        return $this->intelligence;
    }

    public function setIntelligence(int $intelligence): static
    {
        $this->intelligence = $intelligence;

        return $this;
    }

    public function getWisdom(): ?int
    {
        return $this->wisdom;
    }

    public function setWisdom(int $wisdom): static
    {
        $this->wisdom = $wisdom;

        return $this;
    }

    public function getCharisma(): ?int
    {
        return $this->charisma;
    }

    public function setCharisma(int $charisma): static
    {
        $this->charisma = $charisma;

        return $this;
    }

    public function getHealthPoints(): ?int
    {
        return $this->healthPoints;
    }

    public function setHealthPoints(int $healthPoints): static
    {
        $this->healthPoints = $healthPoints;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): static
    {
        $this->race = $race;

        return $this;
    }

    public function getCharacterClass(): ?CharacterClass
    {
        return $this->characterClass;
    }

    public function setCharacterClass(?CharacterClass $characterClass): static
    {
        $this->characterClass = $characterClass;

        return $this;
    }

    /**
     * @return Collection<int, Party>
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Party $party): static
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
        }

        return $this;
    }

    public function removeParty(Party $party): static
    {
        $this->parties->removeElement($party);

        return $this;
    }

    public function validatePointBuy(ExecutionContextInterface $context): void
    {
        $stats = [
            $this->strength,
            $this->dexterity,
            $this->constitution,
            $this->intelligence,
            $this->wisdom,
            $this->charisma,
        ];

        if (in_array(null, $stats, true)) {
            return;
        }

        $totalCost = array_sum(array_map(
            static fn (int $value): int => $value - 8,
            $stats
        ));

        if ($totalCost > 27) {
            $context->buildViolation('La repartition des caracteristiques depasse 27 points.')
                ->atPath('strength')
                ->addViolation();
        }
    }
}
