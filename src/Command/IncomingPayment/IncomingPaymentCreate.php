<?php

namespace App\Command\IncomingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1

/**
 * Class IncomingPaymentCreate
 * @package App\Command\IncomingPayment
 *
 * This command is used to create an incoming payment.
 * It outputs the incoming payment object.
 */
class IncomingPaymentCreate extends Command
{
    protected static $defaultName = 'ip:create';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to create an incoming payment.')
            ->setHelp('This command outputs the incoming payment object.')
            ->addArgument(
                'INCOMING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'Access token for the incoming payment received from the incoming payment grant.',
                $_ENV['INCOMING_PAYMENT_GRANT_ACCESS_TOKEN']
            ) // Required argument
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('INCOMING_PAYMENT_GRANT_ACCESS_TOKEN');

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS,
            $PRIVATE_KEY,
            $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        //@! start chunk 3 | title=Create incoming payment
        $newIncomingPayment = $opClient->incomingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            [
                'walletAddress' => 'https://ilp.interledger-test.dev/my-sg-dollars',
                'incomingAmount' => [
                    'value' => "130",
                    'assetCode' => 'USD',
                    'assetScale' => 2
                ],
                'metadata' => [
                    'description' => 'Test inoming payment to usd account',
                    'externalRef' => 'INVOICE-' . uniqid()
                ],
                'expiresAt' => (new \DateTime())->add(new \DateInterval('PT59M'))->format("Y-m-d\TH:i:s.v\Z")
            ]
        );
        //@! end chunk 3

        $output->writeln('INCOMING_PAYMENT ' . print_r($newIncomingPayment, true));
        //@! start chunk 4 | title=Output
        $output->writeln('INCOMING_PAYMENT_URL: ' . $newIncomingPayment->id);
        //@! end chunk 4

        return Command::SUCCESS;
    }
}
