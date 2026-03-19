<?php

namespace App\DataFixtures;

use App\Entity\Skill;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DefaultSkill extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $acrobatics = new Skill();
        $acrobatics->setName('Acrobaties');
        $acrobatics->setAbility('DEX');

        $arcana = new Skill();
        $arcana->setName('Arcanes');
        $arcana->setAbility('INT');

        $athletics = new Skill();
        $athletics->setName('Athlétisme');
        $athletics->setAbility('STR');

        $stealth = new Skill();
        $stealth->setName('Discrétion');
        $stealth->setAbility('DEX');

        $animalHandling = new Skill();
        $animalHandling->setName('Dressage');
        $animalHandling->setAbility('WIS');

        $sleightOfHand = new Skill();
        $sleightOfHand->setName('Escamotage');
        $sleightOfHand->setAbility('DEX');

        $history = new Skill();
        $history->setName('Histoire');
        $history->setAbility('INT');

        $intimidation = new Skill();
        $intimidation->setName('Intimidation');
        $intimidation->setAbility('CHA');

        $investigation = new Skill();
        $investigation->setName('Investigation');
        $investigation->setAbility('INT');

        $medicine = new Skill();
        $medicine->setName('Médecine');
        $medicine->setAbility('WIS');

        $nature = new Skill();
        $nature->setName('Nature');
        $nature->setAbility('INT');

        $perception = new Skill();
        $perception->setName('Perception');
        $perception->setAbility('WIS');

        $insight = new Skill();
        $insight->setName('Perspicacité');
        $insight->setAbility('WIS');

        $persuasion = new Skill();
        $persuasion->setName('Persuasion');
        $persuasion->setAbility('CHA');

        $religion = new Skill();
        $religion->setName('Religion');
        $religion->setAbility('INT');

        $performance = new Skill();
        $performance->setName('Représentation');
        $performance->setAbility('CHA');

        $survival = new Skill();
        $survival->setName('Survie');
        $survival->setAbility('WIS');

        $deception = new Skill();
        $deception->setName('Tromperie');
        $deception->setAbility('CHA');

        $manager->persist($acrobatics);
        $manager->persist($arcana);
        $manager->persist($athletics);
        $manager->persist($stealth);
        $manager->persist($animalHandling);
        $manager->persist($sleightOfHand);
        $manager->persist($history);
        $manager->persist($intimidation);
        $manager->persist($investigation);
        $manager->persist($medicine);
        $manager->persist($nature);
        $manager->persist($perception);
        $manager->persist($insight);
        $manager->persist($persuasion);
        $manager->persist($religion);
        $manager->persist($performance);
        $manager->persist($survival);
        $manager->persist($deception);

        $manager->flush();
    }
}
