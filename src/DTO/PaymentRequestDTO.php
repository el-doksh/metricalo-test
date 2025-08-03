<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentRequestDTO
{
    // TODO secure cardnumber, year,month, cvc ...so on
    // TODO check validation again , not working for last 4 variables
    public function __construct(
        #[Assert\NotBlank(message: "Amount is required")]
        #[Assert\Type(type: 'float', message: "Amount must be a number")]
        #[Assert\Positive]
        public float $amount,

        #[Assert\NotBlank(message: "Currency is required")]
        #[Assert\Choice(choices: ['USD', 'EUR', 'EGP'], message: "Invalid currency")]
        public string $currency,

        #[Assert\NotBlank(message: "Card Holder Name is required")]
        #[Assert\Type('string', message: "Card Holder Name must be a text")]
        public string $cardHolderName,
        
        #[Assert\NotBlank(message: "Card number is required")]
        #[Assert\Type(type: 'number', message: "Card Number must be a number")]
        #[Assert\length(min: 16)]
        public int $cardNumber,

        #[Assert\NotBlank(message: "Card Expiry month is required")]
        #[Assert\Type(type: 'numeric', message: "Card Expiry month must be a number")]
        #[Assert\length(min: 2, max: 2)]
        #[Assert\Range(min: 1, max: 12, notInRangeMessage: 'Card Expiry month must be between {{ min }} and {{ max }} months',)]
        public int $cardExpMonth,

        #[Assert\NotBlank(message: "Card Expiry year is required")]
        #[Assert\Type(type: 'numeric', message: "Card Expiry year must be a number")]
        #[Assert\length(min: 4, max:4, message: 'Card Expiry year should be at least 4 numbers')]
        public int $cardExpYear,

        #[Assert\NotBlank(message: "Card cvc is required")]
        #[Assert\Type(type: 'number', message: "Card cvc must be a number")]
        #[Assert\length(min: 3, max: 3)]
        public int $cardCvc
    ) {}
}
