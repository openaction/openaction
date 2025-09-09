<?php

namespace App\Repository;

use App\Entity\Organization;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $hasher)
    {
        parent::__construct($registry, User::class);

        $this->hasher = $hasher;
    }

    /*
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->changePassword($newHashedPassword);

        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'm', 'o')
            ->leftJoin('u.memberships', 'm')
            ->leftJoin('m.organization', 'o')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find users related to an organization. An additional parameter can be passed to fetch only admin (or non admin) users.
     *
     * @param Organization $organization Organization context.
     * @param bool         $isAdmin      If null returns all members, if true returns only admin members, if false returns only non-admin members.
     *
     * @return User[]
     */
    public function findOrganizationMembers(Organization $organization, ?bool $isAdmin = null): array
    {
        $builder = $this->createQueryBuilder('u')
            ->select('u', 'm', 'o')
            ->leftJoin('u.memberships', 'm')
            ->leftJoin('m.organization', 'o')
            ->where('o.id = :organization')
            ->setParameter('organization', $organization->getId())
        ;
        if (is_bool($isAdmin)) {
            $builder
                ->andWhere($builder->expr()->eq('m.isAdmin', ':admin'))
                ->setParameter('admin', $isAdmin)
            ;
        }

        return $builder->getQuery()->getResult();
    }

    public function createUserAccount(
        string $email,
        string $firstName,
        string $lastName,
        string $password,
        string $locale = 'en',
    ): User {
        $account = new User($email, $firstName, $lastName, $locale);
        $account->changePassword($this->hasher->hashPassword($account, $password));

        $this->_em->persist($account);

        return $account;
    }
}
