<?php

namespace App\Technical\User;

use InvalidArgumentException;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class RegisterUserDto extends Data
{
    private string $username;
    private string $password;
    private ?string $firstName;
    private ?string $lastName;

    public function __construct(
        string $username,
        string $password,
        ?string $first_name = null,
        ?string $last_name = null
    ) {
        if (empty(trim($username))) {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if (empty(trim($password))) {
            throw new InvalidArgumentException('Password cannot be empty');
        }

        if (strlen($username) > 255) {
            throw new InvalidArgumentException('Username cannot be longer than 255 characters');
        }

        if (strlen(trim($password)) < 8 || strlen($password) > 255) {
            throw new InvalidArgumentException('Password must be between 8 and 255 characters');
        }

        if (strlen(trim(($first_name ?? ''))) == 0) {
            $first_name = null;
        }

        if (strlen(trim(($last_name ?? ''))) == 0) {
            $last_name = null;
        }
        $this->username = $username;
        $this->password = $password;
        $this->firstName = $first_name;
        $this->lastName = $last_name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }
}
