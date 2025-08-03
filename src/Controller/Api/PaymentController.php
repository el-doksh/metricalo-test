<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Service\Payment\PaymentProcessor;

final class PaymentController extends BaseController
{
    #[Route('/api/payment/{provider}', name: 'app_api_payment', methods: ['POST'])]
    public function charge(string $provider, #[MapRequestPayload] PaymentRequestDTO $paymentRequestDTO, PaymentProcessor $paymentProcessor) : JsonResponse
    {
        // TODO unit test...
        try {
            $paymentResponse = $paymentProcessor->process($provider, $paymentRequestDTO);
            if($paymentResponse instanceof PaymentResponseDTO) {
                return $this->successResponse($paymentResponse, 'Successfully received');
            }

            $message = $paymentResponse->message;
            return $this->errorResponse([], $message);
        } catch (\Throwable $th) {
            return $this->errorResponse([], 'Your purchase is not completed, please try again');
        }

    }
}
