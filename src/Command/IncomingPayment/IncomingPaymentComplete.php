<?php
namespace App\Command\IncomingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthenticatedClient;
use OpenPayments\Config\Config;

class IncomingPaymentComplete extends Command
{
    protected static $defaultName = 'ip:complete';

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

      
        $incomingPayment = $incomingPaymentService->complete($INCOMING_PAYMENT_URL);
        
        echo "COMPLETE INCOMING PAYMENT:<br><pre>".print_r($incomingPayment, true)."</pre>";

       // $output->writeln('INCOMING_PAYMENT_GRANT: '.$newIncomingPayment->access_token->value);

        return Command::SUCCESS;
    }
}