<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\DTO\ErrorDTO;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Service\Payment\PaymentProcessor;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class PaymentController extends BaseController
{
    #[Route('/api/payment/{provider}', name: 'app_api_payment', methods: ['POST'])]
    public function __invoke(string $provider, Request $request, ValidatorInterface $validator, PaymentProcessor $paymentProcessor) : JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $paymentRequestDTO = new PaymentRequestDTO(...$requestData);            
            $violations = $validator->validate($paymentRequestDTO);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    array_push($errors, [$violation->getPropertyPath() => $violation->getMessage()]);
                }                
                return $this->errorResponse($errors, 'Validation Errors');
            }

            $paymentResponse = $paymentProcessor->process($provider, $paymentRequestDTO);
            if($paymentResponse instanceof PaymentResponseDTO) {
                return $this->successResponse($paymentResponse, 'Payment completed');
            }

            $message = $paymentResponse->message;
            return $this->errorResponse([], $message);
        } catch (\Throwable $th) {
            return $this->errorResponse([], 'Your purchase is not completed, please try again');
        }

    }
}
