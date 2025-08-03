<?php

namespace App\DTO;

class ErrorDTO
{
 public function __construct(
        public string $message,
    ) {}
}
