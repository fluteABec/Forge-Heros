<?php

namespace App\DataFixtures;

use App\Entity\CharacterClass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DefaultCharacterClass extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        try {
            $barbarian = new CharacterClass();
            $barbarian->setName('Barbare');
            $barbarian->setDescription('Guerrier sauvage animé par une rage dévastatrice.');
            $barbarian->setHealthDice(12);

            $bard = new CharacterClass();
            $bard->setName('Barde');
            $bard->setDescription('Artiste et conteur dont la musique possède un pouvoir magique.');
            $bard->setHealthDice(8);

            $cleric = new CharacterClass();
            $cleric->setName('Clerc');
            $cleric->setDescription('Serviteur divin canalisant la puissance de sa divinité.');
            $cleric->setHealthDice(8);

            $druid = new CharacterClass();
            $druid->setName('Druide');
            $druid->setDescription('Gardien de la nature capable de se métamorphoser.');
            $druid->setHealthDice(8);

            $fighter = new CharacterClass();
            $fighter->setName('Guerrier');
            $fighter->setDescription('Maître des armes et des tactiques de combat.');
            $fighter->setHealthDice(10);

            $mage = new CharacterClass();
            $mage->setName('Mage');
            $mage->setDescription('Érudit de l\'arcane maîtrisant de puissants sortilèges.');
            $mage->setHealthDice(6);

            $paladin = new CharacterClass();
            $paladin->setName('Paladin');
            $paladin->setDescription('Chevalier sacré combinant prouesse martiale et magie divine.');
            $paladin->setHealthDice(10);

            $ranger = new CharacterClass();
            $ranger->setName('Ranger');
            $ranger->setDescription('Chasseur et pisteur expert des terres sauvages.');
            $ranger->setHealthDice(10);

            $sorcerer = new CharacterClass();
            $sorcerer->setName('Sorcier');
            $sorcerer->setDescription('Lanceur de sorts dont le pouvoir est inné et instinctif.');
            $sorcerer->setHealthDice(6);

            $rogue = new CharacterClass();
            $rogue->setName('Voleur');
            $rogue->setDescription('Spécialiste de la discrétion, du crochetage et des attaques sournoises.');
            $rogue->setHealthDice(8);

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
        } catch (\Exception $e) {

        }
    }
}
