<?php

namespace App\DataFixtures;

use App\Entity\CharacterClass;
use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class DefaultCharacterClass extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Chaque classe recoit ici entre 2 et 4 competences pour alimenter l'app et l'API
        $barbarian = new CharacterClass();
        $barbarian->setName('Barbare');
        $barbarian->setDescription('Guerrier sauvage anime par une rage devastatrice.');
        $barbarian->setHealthDice(12);
        $this->addSkills($barbarian, ['athletics', 'intimidation', 'perception']);

        $bard = new CharacterClass();
        $bard->setName('Barde');
        $bard->setDescription('Artiste et conteur dont la musique possede un pouvoir magique.');
        $bard->setHealthDice(8);
        $this->addSkills($bard, ['performance', 'persuasion', 'history', 'deception']);

        $cleric = new CharacterClass();
        $cleric->setName('Clerc');
        $cleric->setDescription('Serviteur divin canalisant la puissance de sa divinite.');
        $cleric->setHealthDice(8);
        $this->addSkills($cleric, ['medicine', 'religion', 'insight']);

        $druid = new CharacterClass();
        $druid->setName('Druide');
        $druid->setDescription('Gardien de la nature capable de se metamorphoser.');
        $druid->setHealthDice(8);
        $this->addSkills($druid, ['nature', 'animal_handling', 'survival', 'medicine']);

        $fighter = new CharacterClass();
        $fighter->setName('Guerrier');
        $fighter->setDescription('Maitre des armes et des tactiques de combat.');
        $fighter->setHealthDice(10);
        $this->addSkills($fighter, ['athletics', 'perception']);

        $mage = new CharacterClass();
        $mage->setName('Mage');
        $mage->setDescription('Erudit de l\'arcane maitrisant de puissants sortileges.');
        $mage->setHealthDice(6);
        $this->addSkills($mage, ['arcana', 'history', 'investigation', 'religion']);

        $paladin = new CharacterClass();
        $paladin->setName('Paladin');
        $paladin->setDescription('Chevalier sacre combinant prouesse martiale et magie divine.');
        $paladin->setHealthDice(10);
        $this->addSkills($paladin, ['athletics', 'persuasion', 'religion', 'medicine']);

        $ranger = new CharacterClass();
        $ranger->setName('Ranger');
        $ranger->setDescription('Chasseur et pisteur expert des terres sauvages.');
        $ranger->setHealthDice(10);
        $this->addSkills($ranger, ['survival', 'perception', 'nature', 'animal_handling']);

        $sorcerer = new CharacterClass();
        $sorcerer->setName('Sorcier');
        $sorcerer->setDescription('Lanceur de sorts dont le pouvoir est inne et instinctif.');
        $sorcerer->setHealthDice(6);
        $this->addSkills($sorcerer, ['arcana', 'deception', 'persuasion']);

        $rogue = new CharacterClass();
        $rogue->setName('Voleur');
        $rogue->setDescription('Specialiste de la discretion, du crochetage et des attaques sournoises.');
        $rogue->setHealthDice(8);
        $this->addSkills($rogue, ['stealth', 'sleight_of_hand', 'acrobatics', 'deception']);

        $manager->persist($barbarian);
        $manager->persist($bard);
        $manager->persist($cleric);
        $manager->persist($druid);
        $manager->persist($fighter);
        $manager->persist($mage);
        $manager->persist($paladin);
        $manager->persist($ranger);
        $manager->persist($sorcerer);
        $manager->persist($rogue);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DefaultSkill::class,
        ];
    }

    private function addSkills(CharacterClass $characterClass, array $skillReferences): void
    {
        // Resout les references declarees dans DefaultSkill pour eviter de dupliquer les objets Skill
        foreach ($skillReferences as $skillReference) {
            $characterClass->addSkill($this->getReference('skill_'.$skillReference, Skill::class));
        }
    }
}
