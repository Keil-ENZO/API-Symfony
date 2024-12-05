<?php
// api/src/State/UserPasswordHasher.php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

/**
 * @implements ProcessorInterface<User, User|void>
 */
final class UserPasswordHasher implements ProcessorInterface
{
    private ProcessorInterface $processor;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        ProcessorInterface $processor,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->processor = $processor;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param User $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        if (!$data instanceof User) {
            throw new \InvalidArgumentException('Expected instance of User.');
        }

        if (!$data->getPlainPassword()) {
            return $this->processor->process($data, $operation, $uriVariables, $context);
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            $data->getPlainPassword()
        );
        $data->setPassword($hashedPassword);
        $data->eraseCredentials();

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
