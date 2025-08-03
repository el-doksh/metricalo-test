<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\DTO\PaymentRequestDTO;
use App\DTO\PaymentResponseDTO;
use App\Service\Payment\PaymentProcessor;

#[AsCommand(
    name: 'app:payment-charge',
    description: 'Charge payment using ACI or Shift4 payment providers',
)]
class PaymentChargeCommand extends Command
{
    private PaymentProcessor $paymentProcessor;
    public function __construct(PaymentProcessor $paymentProcessor)
    {
        parent::__construct();
        $this->paymentProcessor = $paymentProcessor;
    }

    protected function configure(): void
    {
        // TODO add some validations
        
        $this
            ->addArgument('provider', InputArgument::REQUIRED, 'set provider name.')
            ->addOption('amount', null, InputOption::VALUE_REQUIRED, 'Amount in major units (e.g., 10.00)')
            ->addOption('currency', null, InputOption::VALUE_REQUIRED, 'Currency code (e.g., USD)')
            ->addOption('card-number', null, InputOption::VALUE_REQUIRED, 'Card number')
            ->addOption('card-holder', null, InputOption::VALUE_REQUIRED, 'Card holder name')
            ->addOption('exp-month', null, InputOption::VALUE_REQUIRED, 'Expiration month (MM)')
            ->addOption('exp-year', null, InputOption::VALUE_REQUIRED, 'Expiration year (YYYY)')
            ->addOption('cvc', null, InputOption::VALUE_REQUIRED, 'Card cvc');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $provider = $input->getArgument('provider');
        $amount = $input->getOption('amount');
        $currency = $input->getOption('currency');
        $cardHolderName = $input->getOption('card-holder');
        $cardNumber = $input->getOption('card-number');
        $cardExpMonth = $input->getOption('exp-month');
        $cardExpYear = $input->getOption('exp-year');
        $cardCvc = $input->getOption('cvc');

        $allowedProviders = ['aci', 'shift4'];
        if (!in_array($provider, $allowedProviders)) {
            $io->error("Invalid provider '$provider'. Allowed values: " . implode(', ', $allowedProviders));
            return Command::FAILURE;
        }

        if ( !$amount || !$currency || !$cardHolderName || !$cardNumber || !$cardExpMonth || !$cardExpYear || !$cardCvc) {
            $io->error('Missing one or more required payment options.');
            return Command::FAILURE;
        }

        $paymentRequestDTO = new PaymentRequestDTO(
            amount: $amount,
            currency: $currency,
            cardNumber: $cardNumber,
            cardHolderName: $cardHolderName,
            cardExpMonth: $cardExpMonth,
            cardExpYear: $cardExpYear,
            cardCvc: $cardCvc,
        );
        $paymentResponse = $this->paymentProcessor->process($provider, $paymentRequestDTO);
        if($paymentResponse instanceof PaymentResponseDTO) {
            $io->success("Your purchasing has been completed with transaction Id = $paymentResponse->transactionId");

            return Command::SUCCESS;
        }

        $message = $paymentResponse->message;

        $io->error($message);
        return Command::FAILURE;
    }
}
