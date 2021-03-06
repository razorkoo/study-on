<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class BillingUser implements UserInterface
{
    private $email;
    private $token;
    private $roles = [];
    private $refreshToken​;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function setToken($token): self
    {
        $this->token = $token;
        return $this;
    }
    public function getToken(): ?string
    {
        return $this->token;
    }
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken​;
    }
    public function setRefreshToken($refreshToken): self
    {
        $this->refreshToken​ = $refreshToken;
        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
