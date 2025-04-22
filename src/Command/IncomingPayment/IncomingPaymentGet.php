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

class IncomingPaymentGet extends Command
{
    protected static $defaultName = 'ip:get';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
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
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('INCOMING_PAYMENT_GRANT_ACCESS_TOKEN: '.$INCOMING_PAYMENT_GRANT_ACCESS_TOKEN);

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
        echo "GET INCOMING PAYMENT:<br><pre>".print_r($incomingPayment, true)."</pre>";
        //@! end chunk 4
       
        return Command::SUCCESS;
    }
}