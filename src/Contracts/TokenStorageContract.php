<?php

declare(strict_types=1);

namespace Eamirgh\Basalam\Contracts;

use Eamirgh\Basalam\DTOs\TokenData;

interface TokenStorageContract
{
    public function get(): ?TokenData;

    public function set(TokenData $token): void;

    public function clear(): void;
}
