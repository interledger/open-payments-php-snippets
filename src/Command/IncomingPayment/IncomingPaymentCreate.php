<?php
namespace App\Command\IncomingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthenticatedClient;
use OpenPayments\Config\Config;

class IncomingPaymentCreate extends Command
{
    protected static $defaultName = 'ip:create';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'INCOMING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_GRANT_ACCESS_TOKEN']) // Required argument
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('INCOMING_PAYMENT_GRANT_ACCESS_TOKEN');
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('INCOMING_PAYMENT_GRANT_ACCESS_TOKEN: '.$INCOMING_PAYMENT_GRANT_ACCESS_TOKEN);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthenticatedClient($config);
        $walletService = $opClient->walletAddress();

        $wallet  = $walletService->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        
        $incomingPaymentService = $opClient->incomingPayment(
            $wallet->getResourceServer(),
            $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN
        );

        $incomingPaymentRequest = [
            'walletAddress' => $config->getWalletAddressUrl(),
            'incomingAmount' => [
                'value' => "130",
                'assetCode' => 'USD',
                'assetScale' => 2
            ],
            'metadata' => [
                'description' => 'Test php snippets transaction',
                'externalRef' => 'INVOICE-'.uniqid()
            ],
            'expiresAt' => (new \DateTime())->add(new \DateInterval('PT10M'))->format("Y-m-d\TH:i:s.v\Z")
        ];
            
        $newIncomingPayment = $incomingPaymentService->create($incomingPaymentRequest);
        
        echo "GRANT request response:<br><pre>".print_r($newIncomingPayment, true)."</pre>";

       $output->writeln('INCOMING_PAYMENT_URL: '.$newIncomingPayment->getId());

        return Command::SUCCESS;
    }
}