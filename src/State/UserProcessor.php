<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    /**
     * @param User      $data
     * @param Operation $operation
     * @param array     $uriVariables
     * @param array     $context
     *
     * @return mixed
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        if (strlen($data->plainPassword) > 0) {
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->plainPassword));
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
