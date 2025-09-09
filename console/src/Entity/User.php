<?php

namespace App\Entity;

use App\Entity\Integration\TelegramAppAuthorization;
use App\Entity\Model\NotificationSettings;
use App\Entity\Model\PartnerMenu;
use App\Form\User\Model\AccountData;
use App\Repository\UserRepository;
use App\Util\Uid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hidehalo\Nanoid\Client;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfiguration;
use Scheb\TwoFactorBundle\Model\Totp\TotpConfigurationInterface;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use function Symfony\Component\String\u;

use Symfony\Component\Uid\Uuid;

/**
 * A user is a person having access to the Console.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table('users')]
#[UniqueEntity(fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface, TwoFactorInterface, BackupCodeInterface
{
    use Util\EntityIdTrait;
    use Util\EntityUuidTrait;
    use Util\EntityTimestampableTrait;

    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 100)]
    private string $firstName;

    #[ORM\Column(length: 100)]
    private string $lastName;

    #[ORM\Column(type: 'boolean')]
    private bool $isAdmin = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPartner = false;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $partnerName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $partnerMenu = null;

    #[ORM\Column(length: 250)]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $twoFactorEnabled = false;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $twoFactorAuthSecret;

    #[ORM\Column(type: 'simple_array', nullable: true)]
    private ?array $twoFactorBackupCodes = [];

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $picture;

    /**
     * Language to use to display the Console for this user.
     */
    #[ORM\Column(length: 6)]
    private string $locale;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $secretResetPassword;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $dueDateResetPassword;

    #[ORM\Column(type: 'json')]
    private array $notificationSettings;

    /**
     * @var OrganizationMember[]|Collection
     */
    #[ORM\OneToMany(targetEntity: OrganizationMember::class, mappedBy: 'member', orphanRemoval: true)]
    private Collection $memberships;

    /**
     * @var UserVisit[]|Collection
     */
    #[ORM\OneToMany(targetEntity: UserVisit::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $visits;

    /**
     * @var TelegramAppAuthorization[]|Collection
     */
    #[ORM\OneToMany(targetEntity: TelegramAppAuthorization::class, mappedBy: 'member', orphanRemoval: true)]
    private Collection $telegramAppsAuthorizations;

    public function __construct(string $email, string $firstName, string $lastName, string $locale = 'en')
    {
        $this->populateTimestampable();
        $this->uuid = Uid::random();
        $this->email = strtolower($email);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->locale = $locale;
        $this->notificationSettings = NotificationSettings::getAllEvents();
        $this->memberships = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->telegramAppsAuthorizations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function __serialize(): array
    {
        return ['id' => $this->id, 'email' => $this->email];
    }

    public function __unserialize(array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
    }

    public static function createFixture(array $data): self
    {
        $self = new User($data['email'], $data['firstName'], $data['lastName']);
        $self->password = $data['password'] ?? 'password';
        $self->isAdmin = $data['isAdmin'] ?? false;
        $self->isPartner = $data['isPartner'] ?? false;
        $self->partnerName = $data['partnerName'] ?? null;
        $self->partnerMenu = $data['partnerMenu'] ?? null;

        if (isset($data['uuid']) && $data['uuid']) {
            $self->uuid = Uuid::fromString($data['uuid']);
        }

        return $self;
    }

    /*
     * Details
     */
    public function applyAccountUpdate(AccountData $data)
    {
        $this->firstName = (string) $data->firstName;
        $this->lastName = (string) $data->lastName;
        $this->locale = (string) $data->locale;
    }

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName.' '.$this->lastName;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getSecretResetPassword(): ?string
    {
        return $this->secretResetPassword;
    }

    public function isDueDateResetPasswordExpired()
    {
        return $this->dueDateResetPassword && $this->dueDateResetPassword < new \DateTime();
    }

    public function getDueDateResetPassword(): ?\DateTimeImmutable
    {
        return $this->dueDateResetPassword;
    }

    /*
     * Security
     */
    public function changePassword(string $hashedPassword)
    {
        $this->password = $hashedPassword;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    public function isPartner(): bool
    {
        return $this->isPartner;
    }

    public function setIsPartner(bool $isPartner)
    {
        $this->isPartner = $isPartner;
    }

    public function getPartnerName(): ?string
    {
        return $this->partnerName;
    }

    public function setPartnerName(?string $partnerName)
    {
        $this->partnerName = $partnerName;
    }

    public function getPartnerMenu(): PartnerMenu
    {
        return PartnerMenu::fromArray($this->partnerMenu ?: []);
    }

    public function setPartnerMenu(PartnerMenu $partnerMenu)
    {
        $this->partnerMenu = $partnerMenu->toArray();
    }

    public function startTwoFactorEnablingProcess(string $secret)
    {
        $this->twoFactorAuthSecret = $secret;
    }

    public function isInTwoFactorEnablingProcess(): bool
    {
        return !$this->twoFactorEnabled && null !== $this->twoFactorAuthSecret;
    }

    public function finishEnablingTwoFactor(array $backupCodes = [])
    {
        if (!$backupCodes) {
            $nano = new Client();

            for ($i = 0; $i < 10; ++$i) {
                $backupCodes[] = $nano->formattedId('0123456789', 6);
            }
        }

        $this->twoFactorEnabled = true;
        $this->twoFactorBackupCodes = $backupCodes;
    }

    public function disableTwoFactor()
    {
        $this->twoFactorEnabled = false;
        $this->twoFactorAuthSecret = null;
        $this->twoFactorBackupCodes = [];
    }

    public function isTotpAuthenticationEnabled(): bool
    {
        return $this->twoFactorEnabled;
    }

    public function isTwoFactorEnabled(): bool
    {
        return $this->twoFactorEnabled;
    }

    public function getTotpAuthenticationUsername(): string
    {
        return $this->email;
    }

    public function getTotpAuthenticationConfiguration(): ?TotpConfigurationInterface
    {
        return new TotpConfiguration($this->twoFactorAuthSecret, TotpConfiguration::ALGORITHM_SHA1, 30, 6);
    }

    public function getGoogleAuthenticatorSecret(): ?string
    {
        return $this->twoFactorAuthSecret;
    }

    public function isBackupCode(string $code): bool
    {
        return \in_array($code, $this->twoFactorBackupCodes, true);
    }

    public function invalidateBackupCode(string $code): void
    {
        if (false !== ($key = array_search($code, $this->twoFactorBackupCodes, true))) {
            array_splice($this->twoFactorBackupCodes, $key, 1);
        }
    }

    public function getTwoFactorBackupCodes(): ?array
    {
        return $this->twoFactorBackupCodes;
    }

    public function createForgotPasswordSecret(string $expiresAt = '+24 hours')
    {
        $this->secretResetPassword = Uid::random();
        $this->dueDateResetPassword = new \DateTimeImmutable($expiresAt);
    }

    public function clearForgotPasswordSecret()
    {
        $this->secretResetPassword = null;
        $this->dueDateResetPassword = null;
    }

    /*
     * Settings
     */
    public function getNotificationSettings(): NotificationSettings
    {
        return new NotificationSettings($this->notificationSettings ?? []);
    }

    public function setNotificationSettings(NotificationSettings $notificationSettings)
    {
        $this->notificationSettings = $notificationSettings->toArray();
    }

    /**
     * @return Collection|TelegramAppAuthorization[]
     */
    public function getTelegramAppsAuthorizations(): Collection
    {
        return $this->telegramAppsAuthorizations;
    }

    /*
     * Organizations
     */
    /**
     * @return Collection|OrganizationMember[]
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    /**
     * @return Organization[]
     */
    public function getOrganizations()
    {
        $ids = [];
        $names = [];
        $registry = [];

        foreach ($this->memberships as $membership) {
            $ids[] = $membership->getOrganization()->getId();
            $names[] = u($membership->getOrganization()->getName())->lower();
            $registry[$membership->getOrganization()->getId()] = $membership->getOrganization();
        }

        // array_multisort raises an error ("Error: Nesting level too deep") if it manipulates recursive
        // objects: sort IDs then remap the list.
        array_multisort($names, SORT_NATURAL, SORT_ASC, $ids);

        $orgas = [];
        foreach ($ids as $id) {
            $orgas[] = $registry[$id];
        }

        return $orgas;
    }

    /*
     * Visits
     */
    /**
     * @return Collection|UserVisit[]
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function getLastVisitDate(): ?\DateTime
    {
        $lastVisit = null;
        foreach ($this->visits as $visit) {
            if (!$lastVisit || $visit->getDate() > $lastVisit) {
                $lastVisit = $visit->getDate();
            }
        }

        return $lastVisit;
    }

    public function getLastWeekPageViews(): array
    {
        $pageViews = [];
        foreach ($this->visits as $visit) {
            $pageViews[$visit->getDate()->format('Y-m-d')] = $visit->getPageViews();
        }

        $week = [];
        for ($i = 6; $i >= 0; --$i) {
            $date = (new \DateTime($i.' days ago'))->format('Y-m-d');
            $week[$date] = $pageViews[$date] ?? 0;
        }

        return $week;
    }

    /*
     * UserInterface
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof self && $user->getId() === $this->id;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isAdmin || $this->isPartner) {
            $roles[] = 'ROLE_PARTNER';
        }

        return $roles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
    }
}
