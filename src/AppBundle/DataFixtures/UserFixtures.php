<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('johndoe');

        $password = $this->encoder->encodePassword($user, 'test');
        $user->setPassword($password);
        $user->setEmail('johndoe@test.com');
        $user->setEnabled(1);

        $manager->persist($user);
        $manager->flush();

    }
}