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
 * Class OutgoingPaymentGet
 * @package App\Command\OutgoingPayment
 *
 * This command is used to get an outgoing payment.
 * It outputs the outgoing payment object.
 */
class OutgoingPaymentGet extends Command
{
    protected static $defaultName = 'op:get';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to get an outgoing payment.')
            ->setHelp('This command  outputs the outgoing payment object.')
            ->addArgument(
                'OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'Access token for the outgoing payment received from the outgoing payment grant.',
                $_ENV['OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN'] ?? null
            )
            ->addArgument(
                'OUTGOING_PAYMENT_URL',
                InputArgument::OPTIONAL,
                'The url of the outgoing payment.',
                $_ENV['OUTGOING_PAYMENT_URL'] ??  null
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN');
        $OUTGOING_PAYMENT_URL = $input->getArgument('OUTGOING_PAYMENT_URL');

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2
        
        //@! start chunk 3 | title=Get outgoing payment
        $outgoingPayment = $opClient->outgoingPayment()->get(
            [
                'access_token' => $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN,
                'url' => $OUTGOING_PAYMENT_URL
            ]
        );
        //@! end chunk 3

        //@! start chunk 4 | title=Output
        $output->writeln('OUTGOING PAYMENT: '.print_r($outgoingPayment, true));
        //@! end chunk 4
        return Command::SUCCESS;
    }
}