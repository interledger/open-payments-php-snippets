<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class OutgoingPaymentCreateAmount extends Command
{
    protected static $defaultName = 'op:create:amount';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN'] ?? null
            )
            ->addArgument(
                'INCOMING_PAYMENT_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_URL']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN');
        $INCOMING_PAYMENT_URL = $input->getArgument('INCOMING_PAYMENT_URL');
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN: '.$OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $wallet  = $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        $outgoingPaymentRequest = [
            'walletAddress' => $config->getWalletAddressUrl(),
            'incomingPayment' => $INCOMING_PAYMENT_URL,
            'debitAmount' => [
                'value' => '121',
                'assetCode' => 'USD',
                'assetScale' => 2
            ],
            'metadata' => [
                'description' => 'Test outgoing payment',
                'reference' => '1234567890',
                'invoiceId' => '1234567890',
                'customData' => [
                    'key1' => 'value1',
                    'key2' => 'value2'
                ]
            ],
        ];
        
        $newOutgoingPayment = $opClient->outgoingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            $outgoingPaymentRequest
        );

        echo "GRANT outgoingPayment  create() response:<br><pre>".print_r($newOutgoingPayment, true)."</pre>";

       $output->writeln('OUTGOING_PAYMENT_URL: '.$newOutgoingPayment->id);

        return Command::SUCCESS;
    }
}