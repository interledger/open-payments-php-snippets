<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class OutgoingPaymentCreate extends Command
{
    protected static $defaultName = 'op:create';

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
                'QUOTE_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['QUOTE_URL'] ?? null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN');
        $QUOTE_URL = $input->getArgument('QUOTE_URL');
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
            'quoteId' => $QUOTE_URL,
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