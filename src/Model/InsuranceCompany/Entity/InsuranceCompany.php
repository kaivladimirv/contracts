<?php

declare(strict_types=1);

namespace App\Model\InsuranceCompany\Entity;

use App\Model\AggregateRootInterface;
use App\Model\EventTrait;
use App\Model\InsuranceCompany\Event\CompanyAccessTokenChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyDeletedEvent;
use App\Model\InsuranceCompany\Event\CompanyEmailChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyNameChangedEvent;
use App\Model\InsuranceCompany\Event\CompanyRegisteredEvent;
use App\Model\InsuranceCompany\Event\CompanyRegistrationConfirmedEvent;
use App\Model\InsuranceCompany\Exception\AccessTokenIsExpiredException;
use App\Model\InsuranceCompany\Exception\EmailConfirmTokenNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\IncorrectConfirmTokenException;
use App\Model\InsuranceCompany\Exception\NameNotSpecifiedException;
use App\Model\InsuranceCompany\Exception\PasswordNotSpecifiedException;
use DateTimeImmutable;

class InsuranceCompany implements AggregateRootInterface
{
    use EventTrait;

    private string $name;
    private Email $email;
    private string $passwordHash      = '';
    private string $emailConfirmToken = '';
    private bool $isEmailConfirmed  = false;
    private ?AccessToken $accessToken = null;
    private bool $isDeleted         = false;

    /**
     * @throws NameNotSpecifiedException
     */
    private function __construct(private readonly InsuranceCompanyId $id, string $name)
    {
        $this->assertNameIsNotEmpty($name);
        $this->name = $name;
    }

    /**
     * @throws EmailConfirmTokenNotSpecifiedException
     * @throws PasswordNotSpecifiedException
     * @throws NameNotSpecifiedException
     */
    public static function register(
        InsuranceCompanyId $id,
        string $name,
        Email $email,
        string $passwordHash,
        string $emailConfirmToken
    ): self {
        if (empty($emailConfirmToken)) {
            throw new EmailConfirmTokenNotSpecifiedException('Не указан токен подтверждения электронного адреса');
        }

        if (empty($passwordHash)) {
            throw new PasswordNotSpecifiedException('Не указан пароль');
        }

        $self = new self($id, $name);

        $self->email = $email;
        $self->passwordHash = $passwordHash;
        $self->emailConfirmToken = $emailConfirmToken;
        $self->isDeleted = false;

        $self->registerEvent(
            new CompanyRegisteredEvent(
                $self->getId(),
                $name,
                $self->email,
                $self->passwordHash,
                $self->emailConfirmToken
            )
        );

        return $self;
    }

    public function getId(): InsuranceCompanyId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getEmailConfirmToken(): string
    {
        return $this->emailConfirmToken;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->isEmailConfirmed;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAccessToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    /**
     * @throws NameNotSpecifiedException
     */
    public function changeName(string $newName): void
    {
        $this->assertNameIsNotEmpty($newName);

        $oldName = $this->name;
        $this->name = $newName;

        if ($newName !== $oldName) {
            $this->registerEvent(
                new CompanyNameChangedEvent(
                    $this->getId(),
                    $oldName,
                    $newName
                )
            );
        }
    }

    /**
     * @throws NameNotSpecifiedException
     */
    private function assertNameIsNotEmpty(string $name): void
    {
        if (empty($name)) {
            throw new NameNotSpecifiedException('Не указано название страховой компании');
        }
    }

    public function changeEmail(Email $newEmail): void
    {
        $oldEmail = $this->email;
        $this->email = $newEmail;

        $this->registerEvent(
            new CompanyEmailChangedEvent(
                $this->getId(),
                $oldEmail,
                $newEmail
            )
        );
    }

    /**
     * @throws IncorrectConfirmTokenException
     */
    public function confirmRegister(string $emailConfirmToken): void
    {
        if ($this->emailConfirmToken !== $emailConfirmToken) {
            throw new IncorrectConfirmTokenException('Некорректный токен');
        }

        $this->isEmailConfirmed = true;
        $this->emailConfirmToken = '';

        $this->registerEvent(
            new CompanyRegistrationConfirmedEvent(
                $this->getId(),
                $emailConfirmToken
            )
        );
    }

    /**
     * @throws AccessTokenIsExpiredException
     */
    public function changeAccessToken(AccessToken $newAccessToken, DateTimeImmutable $date): void
    {
        if ($newAccessToken->isExpiredTo($date)) {
            throw new AccessTokenIsExpiredException('Срок действия токена истек');
        }

        $oldAccessToken = $this->accessToken ?? null;
        $this->accessToken = $newAccessToken;

        $this->registerEvent(
            new CompanyAccessTokenChangedEvent(
                $this->getId(),
                $oldAccessToken,
                $newAccessToken,
                $this->email
            )
        );
    }

    public function delete(): void
    {
        $this->isDeleted = true;

        $this->registerEvent(
            new CompanyDeletedEvent(
                $this->getId()->getValue(),
                $this->name,
                $this->email->getValue()
            )
        );
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function toArray(): array
    {
        return [
            'id'                   => $this->id->getValue(),
            'name'                 => $this->name,
            'email'                => $this->email->getValue(),
            'password_hash'        => $this->passwordHash,
            'email_confirm_token'  => $this->emailConfirmToken,
            'is_email_confirmed'   => $this->isEmailConfirmed ? 1 : 0,
            'access_token'         => !empty($this->accessToken) ? $this->accessToken->getToken() : null,
            'access_token_expires' => !empty($this->accessToken) ? $this->accessToken->getExpires()
                                                                                     ->format('c') : null,
            'is_deleted'           => $this->isDeleted ? 1 : 0,
        ];
    }
}
