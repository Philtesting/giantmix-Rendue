<?php

namespace App\DataFixtures;

use App\Entity\Revendeur;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        $revendeur = new Revendeur();
        $revendeur->setMail('king@king.fr');
        $revendeur->setEntreprise('King BONGO BONG');
        $revendeur->setPassword($this->passwordEncoder->encodePassword(
            $revendeur,
            '12345678'
        ));
        $manager->persist($revendeur);

        $revendeur = new Revendeur();
        $revendeur->setMail('pick@me.fr');
        $revendeur->setEntreprise('Pick me me me');
        $revendeur->setPassword($this->passwordEncoder->encodePassword(
            $revendeur,
            '12345678'
        ));
        $manager->persist($revendeur);

        $user = new User();
        $user->setMail('user1@user1.fr');
        $user->setUserName('Flip flap floup');
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            '12345678'
        ));
        $manager->persist($user);

        $manager->flush();

    }
}
