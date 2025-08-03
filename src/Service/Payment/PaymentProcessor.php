<?php

namespace App\Service\Payment;

use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;
use App\Service\Payment\AciProvider;
use App\Service\Payment\Shift4Provider;

class PaymentProcessor
{
    public function __construct(
        private AciProvider $aci,
        private Shift4Provider $shift4
    ) {}

    public function process(string $provider, PaymentRequestDTO $paymentRequestDTO) : PaymentResponseDTO | ErrorDTO
    {
        return match (strtolower($provider)) {
            'aci' => $this->aci->charge($paymentRequestDTO),
            'shift4' => $this->shift4->charge($paymentRequestDTO),
            default => new ErrorDTO("Unsupported provider: $provider")
        };
    }
}

?>