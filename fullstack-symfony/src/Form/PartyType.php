<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\Party;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ?User $user */
        $user = $options['user'];
        /** @var Party|null $party */
        $party = $options['party'];
        $isOwner = $options['is_owner'];

        if ($isOwner) {
            $builder
                ->add('name')
                ->add('description')
                ->add('maxSize');
        }

        $selectedCharacters = new ArrayCollection();

        if ($party instanceof Party && $user instanceof User) {
            foreach ($party->getCharacters() as $character) {
                if ($character->getUser() === $user) {
                    $selectedCharacters->add($character);
                }
            }
        }

        $builder->add('characters', EntityType::class, [
                'class' => Character::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'mapped' => false,
                'data' => $selectedCharacters,
                'query_builder' => static function (EntityRepository $repository) use ($user) {
                    $qb = $repository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');

                    if ($user instanceof User) {
                        $qb->andWhere('c.user = :user')
                            ->setParameter('user', $user);
                    } else {
                        $qb->andWhere('1 = 0');
                    }

                    return $qb;
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Party::class,
            'user' => null,
            'party' => null,
            'is_owner' => false,
        ]);

        $resolver->setAllowedTypes('user', ['null', User::class]);
        $resolver->setAllowedTypes('party', ['null', Party::class]);
        $resolver->setAllowedTypes('is_owner', 'bool');
    }
}
