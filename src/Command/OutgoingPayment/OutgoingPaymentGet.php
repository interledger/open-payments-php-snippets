<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class OutgoingPaymentGet extends Command
{
    protected static $defaultName = 'op:get';

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
                'OUTGOING_PAYMENT_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
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
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN: '.$OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        
        $outgoingPayment = $opClient->outgoingPayment()->get(
            [
                'access_token' => $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN,
                'url' => $OUTGOING_PAYMENT_URL
            ]
        );

        echo "GET OUTGOING PAYMENT:<br><pre>".print_r($outgoingPayment, true)."</pre>";

        return Command::SUCCESS;
    }
}