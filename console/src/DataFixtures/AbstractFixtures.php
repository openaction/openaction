<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractFixtures extends Fixture implements FixtureGroupInterface, OrderedFixtureInterface
{
    protected EntityManagerInterface $em;
    protected UserPasswordHasherInterface $hasher;

    protected static int $order = 1;
    protected static array $groups = [];

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    abstract protected function doLoad();

    public function getOrder(): int
    {
        return static::$order;
    }

    public static function getGroups(): array
    {
        return static::$groups;
    }

    /**
     * @param EntityManagerInterface $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->em = $manager;
        $this->doLoad();
    }

    protected function findBy(string $class, array $criteria)
    {
        return $this->em->getRepository($class)->findBy($criteria);
    }

    protected function findOneBy(string $class, array $criteria)
    {
        return $this->em->getRepository($class)->findOneBy($criteria);
    }
}
