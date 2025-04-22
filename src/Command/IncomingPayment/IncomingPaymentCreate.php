<?php
namespace App\Command\IncomingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
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
       
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        $incomingPaymentRequest = [
            'walletAddress' => $config->getWalletAddressUrl(),
            // 'incomingAmount' => [
            //     'value' => "130",
            //     'assetCode' => 'USD',
            //     'assetScale' => 2
            // ],
            'metadata' => [
                'description' => 'Test php snippets transaction with $1,30 amount',
                'externalRef' => 'INVOICE-'.uniqid()
            ],
            'expiresAt' => (new \DateTime())->add(new \DateInterval('PT59M'))->format("Y-m-d\TH:i:s.v\Z")
        ];

        $newIncomingPayment = $opClient->incomingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            $incomingPaymentRequest
        );
        
        echo "GRANT request response: ".print_r($newIncomingPayment, true);

       $output->writeln('INCOMING_PAYMENT_URL: '.$newIncomingPayment->id);

        return Command::SUCCESS;
    }
}