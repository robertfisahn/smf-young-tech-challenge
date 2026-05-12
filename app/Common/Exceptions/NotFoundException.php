<?php

declare(strict_types=1);

namespace App\Common\Exceptions;

final class NotFoundException extends AppException
{
    public function __construct(string $resource, string|int $id)
    {
        parent::__construct("Resource '{$resource}' with ID '{$id}' not found.", 404);
    }
}
