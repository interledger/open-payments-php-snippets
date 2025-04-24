<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1
/**
 * Class OutgoingPaymentCreate
 * @package App\Command\OutgoingPayment
 *
 * This command is used to create an outgoing payment.
 * It outputs the outgoing payment object.
 */
class OutgoingPaymentCreate extends Command
{
    protected static $defaultName = 'op:create';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to create an outgoing payment.')
            ->setHelp('This command outputs the outgoing payment object.')
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

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2


        $wallet  = $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        //@! start chunk 3 | title=Create outgoing payment
        $newOutgoingPayment = $opClient->outgoingPayment()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            [
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
            ]
        );
        //@! end chunk 3

        $output->writeln('OUTGOING_PAYMENT '.print_r($newOutgoingPayment, true));
        //@! start chunk 4 | title=Output
        $output->writeln('OUTGOING_PAYMENT_URL '.$newOutgoingPayment->id);
        //@! end chunk 4

        return Command::SUCCESS;
    }
}