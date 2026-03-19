<?php

namespace App\DataFixtures;

use App\Entity\Race;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DefaultRace extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $human = new Race();
        $human->setName('Humain');
        $human->setDescription('Polyvalents et ambitieux, les humains sont la race la plus répandue.');

        $elf = new Race();
        $elf->setName('Elfe');
        $elf->setDescription('Gracieux et longévifs, les elfes possèdent une affinité naturelle avec la magie.');

        $dwarf = new Race();
        $dwarf->setName('Nain');
        $dwarf->setDescription('Robustes et tenaces, les nains sont des artisans et guerriers réputés.');

        $halfelin = new Race();
        $halfelin->setName('Halfelin');
        $halfelin->setDescription('Petits et agiles, les halfelins sont connus pour leur chance et leur discrétion.');

        $halfOrc = new Race();
        $halfOrc->setName('Demi-Orc');
        $halfOrc->setDescription('Forts et endurants, les demi-orcs allient la puissance des orcs à l\'adaptabilité humaine.');

        $gnome = new Race();
        $gnome->setName('Gnome');
        $gnome->setDescription('Curieux et inventifs, les gnomes excellent dans les domaines de la magie et de la technologie.');

        $tiefling = new Race();
        $tiefling->setName('Tieffelin');
        $tiefling->setDescription('Descendants d\'une lignée infernale, les tieffelins portent la marque de leur héritage.');

        $halfElf = new Race();
        $halfElf->setName('Demi-Elfe');
        $halfElf->setDescription('Héritant du meilleur des deux mondes, les demi-elfes sont diplomates et polyvalents.');


        $manager->persist($human);
        $manager->persist($elf);
        $manager->persist($dwarf);
        $manager->persist($halfelin);
        $manager->persist($halfOrc);
        $manager->persist($gnome);
        $manager->persist($tiefling);
        $manager->persist($halfElf);

        $manager->flush();
    }
}
