<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 100)]
        public readonly string $username,
        #[Assert\NotBlank]
        #[Assert\Length(min: 12)]
        #[\SensitiveParameter]
        //TODO: add more validation
        public readonly string $plainPassword
    )
    {
    }
}