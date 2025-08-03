<?php

namespace App\DTO;

class PaymentResponseDTO
{
    public function __construct(
        public string $transactionId,
        public \DateTimeInterface $createdAt,
        public float $amount,
        public string $currency,
        public string $cardBin,
    ) {}
}
