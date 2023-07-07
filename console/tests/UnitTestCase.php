<?php

namespace App\Tests;

use App\Entity\Area;
use App\Entity\Organization;
use App\Entity\Project;
use App\Entity\Theme\WebsiteTheme;
use App\Entity\User;
use App\Platform\Plans;
use App\Util\Uid;
use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected function createAreaTree(): array
    {
        $france = new Area(36778547219895752, null, Area::TYPE_COUNTRY, 'fr', 'France');
        $this->setProperty($france, 'treeLevel', 0);
        $this->setProperty($france, 'treeLeft', 1);
        $this->setProperty($france, 'treeRight', 4);

        $idf = new Area(64795327863947811, $france, Area::TYPE_PROVINCE, 'fr_11', 'Île-de-France');
        $this->setProperty($france, 'treeLevel', 1);
        $this->setProperty($idf, 'treeLeft', 2);
        $this->setProperty($idf, 'treeRight', 3);

        return [
            'france' => $france,
            'idf' => $idf,
        ];
    }

    protected function createUser(int $id, string $email = 'titouan.galopin@citipo.com', bool $isAdmin = false): User
    {
        $user = new User($email, 'Titouan', 'Galopin');
        $this->setProperty($user, 'id', $id);
        $this->setProperty($user, 'uuid', Uid::fixed(User::class.'-'.$id));
        $this->setProperty($user, 'isAdmin', $isAdmin);

        return $user;
    }

    protected function createOrganization(int $id, string $name = 'MockOrganization', string $plan = Plans::ORGANIZATION): Organization
    {
        $orga = new Organization($name);
        $orga->updateSubscriptionStatus(false, new \DateTime('+1 year'));
        $this->setProperty($orga, 'id', $id);
        $this->setProperty($orga, 'uuid', Uid::fixed(Organization::class.'-'.$id));
        $this->setProperty($orga, 'subscriptionPlan', $plan);

        return $orga;
    }

    protected function createProject(int $id, string $name = 'MockProject', Organization $orga = null): Project
    {
        $project = new Project($orga ?: $this->createOrganization(-1), $name, new WebsiteTheme(1, 1, 'citipo/theme'));
        $this->setProperty($project, 'id', $id);
        $this->setProperty($project, 'uuid', Uid::fixed(Project::class.'-'.$id));

        return $project;
    }

    protected function setProperty($object, string $property, $value)
    {
        $reflection = (new \ReflectionObject($object))->getProperty($property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
