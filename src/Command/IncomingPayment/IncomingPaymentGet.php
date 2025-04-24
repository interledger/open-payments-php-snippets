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
 * Class IncomingPaymentGet
 * @package App\Command\IncomingPayment
 *
 * This command is used to get an incoming payment.
 * It outputs the incoming payment object.
 */
class IncomingPaymentGet extends Command
{
    protected static $defaultName = 'ip:get';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to get an incoming payment.')
            ->setHelp('This command outputs the incoming payment object.')
            ->addArgument(
                'INCOMING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_GRANT_ACCESS_TOKEN']
            )
            ->addArgument(
                'INCOMING_PAYMENT_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_URL']
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('INCOMING_PAYMENT_GRANT_ACCESS_TOKEN');
        $INCOMING_PAYMENT_URL = $input->getArgument('INCOMING_PAYMENT_URL');
       
        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Get incoming payment
        $incomingPayment = $opClient->incomingPayment()->get(
            [
                'access_token' => $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN,
                'url' => $INCOMING_PAYMENT_URL
            ]
        );
        //@! end chunk 3
        
        //@! start chunk 4 | title=Output
        $output->writeln('INCOMING PAYMENT: '.print_r($incomingPayment, true));
        //@! end chunk 4
       
        return Command::SUCCESS;
    }
}