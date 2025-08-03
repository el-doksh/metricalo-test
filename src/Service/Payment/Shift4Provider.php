<?php

namespace App\Service\Payment;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;

class Shift4Provider implements PaymentProviderInterface
{
    private string $apiKey;

    public function __construct(private HttpClientInterface $httpClient, private SerializerInterface $serializer) {
        $this->apiKey = 'pr_test_tXHm9qV9qV9bjIRHcQr9PLPa';
    }

    private function getApiKey()
    {
        return 'Basic ' . base64_encode($this->apiKey . ':');
    }

    public function charge(PaymentRequestDTO $paymentRequestDTO): PaymentResponseDTO | ErrorDTO
    {
        try {
            $url = 'https://api.shift4.com/charges';
            $data = [
                'amount' => number_format($paymentRequestDTO->amount * 100, 0, '', ''), // Amount in minor units (e.g., 1000 for $10.00)
                'currency' => $paymentRequestDTO->currency,
                'card' => [
                    'cardholderName' => $paymentRequestDTO->cardHolderName,
                    'number' => '4242424242424242', // Test card number
                    'expMonth' => "$paymentRequestDTO->cardExpMonth",
                    'expYear' => "$paymentRequestDTO->cardExpYear",
                    'cvc' => "$paymentRequestDTO->cardCvc",
                ],
                'description' => 'Charging test'
            ];

            $response = $this->httpClient->request('POST', $url, [
                'body' => http_build_query($data),
                'headers' => [
                    'Authorization' => $this->getApiKey(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            $responseData = json_decode($response->getContent(false), true);
            if(isset($responseData['error'])) {
                return new ErrorDTO(
                    message: $responseData['error']['message']
                );
            }

            return new PaymentResponseDTO(
                transactionId: $responseData['id'],
                createdAt: new \DateTimeImmutable('@' . $responseData['created']),
                amount: number_format($responseData['amount'] / 100, 2, '.', ''),
                currency: $responseData['currency'],
                cardBin: $responseData['card']['first6'],
            );
        } catch (\Throwable $th) {
            return new ErrorDTO(
                message: 'Error while purchasing'
            );
        }
    }
}
