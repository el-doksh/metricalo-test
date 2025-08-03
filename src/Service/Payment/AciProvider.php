<?php

namespace App\Service\Payment;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;

class AciProvider implements PaymentProviderInterface
{
    private string $entityId;
    private string $bearerToken;

    public function __construct(private HttpClientInterface $httpClient, private SerializerInterface $serializer) {
        $this->entityId = '8ac7a4c79394bdc801939736f17e063d'; 
        $this->bearerToken = 'Bearer OGFjN2E0Yzc5Mzk0YmRjODAxOTM5NzM2ZjFhNzA2NDF8enlac1lYckc4QXk6bjYzI1NHNng=';
    }
    
    private function getEntityId()
    {
        return $this->entityId;
    }

    private function getBearerToken()
    {
        return $this->bearerToken;
    }

    private function successCode()
    {
        return '000.100.110';
    }

    public function charge(PaymentRequestDTO $paymentRequestDTO): PaymentResponseDTO | ErrorDTO
    {
        try {
            // DNE
            // TODO ERRDTO msh 3agbany
            $url = "https://eu-test.oppwa.com/v1/payments";
            $data = [
                'entityId' => $this->getEntityId(),
                'amount' => number_format($paymentRequestDTO->amount, 2, '.', ''),
                'currency' => 'EUR',
                'paymentType' => 'PA',
                'paymentBrand' => 'VISA',
                'card.number' => '4200000000000000',
                'card.holder' => $paymentRequestDTO->cardHolderName,
                'card.expiryMonth' => "$paymentRequestDTO->cardExpMonth",
                'card.expiryYear' => "$paymentRequestDTO->cardExpYear",
                'card.cvv' => "$paymentRequestDTO->cardCvc",
            ];

            $response = $this->httpClient->request('POST', $url, [
                'body' => http_build_query($data),
                'headers' => [
                    'Authorization' => $this->getBearerToken(),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ]
            ]);

            $responseData = json_decode($response->getContent(false), true);
            $resultCode = $responseData['result']['code'];
            $resultMessage = $responseData['result']['description'];

            if($resultCode == $this->successCode()) {
                return new PaymentResponseDTO(
                    transactionId: $responseData['id'],
                    createdAt: new \DateTimeImmutable($responseData['timestamp']),
                    amount: $responseData['amount'],
                    currency: $responseData['currency'],
                    cardBin: $responseData['card']['bin'],
                );
            } 
            return new ErrorDTO(
                message: $resultMessage,
            );

        } catch (\Throwable $th) {
            return new ErrorDTO(
                message: 'Error while purchasing'
            );
        }
    }
}
