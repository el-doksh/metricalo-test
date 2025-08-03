<?php

namespace App\Service\Payment;

use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;

interface PaymentProviderInterface
{
    public function charge(PaymentRequestDTO $request): PaymentResponseDTO | ErrorDTO;
}