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
 * Class IncomingPaymentList
 * @package App\Command\IncomingPayment
 *
 * This command is used to list incoming payments.
 * It outputs the incoming payment object.
 */

class IncomingPaymentList extends Command
{
    protected static $defaultName = 'ip:list';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'INCOMING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_GRANT_ACCESS_TOKEN'])
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
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2
       
        //@! start chunk 3 | title=Get wallet address
        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        //@! end chunk 3
        
        //@! start chunk 4 | title=List incoming payments
        $incomingPaymentsList = $opClient->incomingPayment()->list(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $INCOMING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            [
                'wallet-address' => $config->getWalletAddressUrl(),
                'first' => 10,
                'start'=> '96d964f0-3421-4df0-bb04-cb8d653bc571'
            ]
        );
        //@! end chunk 4

        //@! start chunk 5 | title=Output
        $output->writeln('INCOMING PAYMENTS '.print_r($incomingPaymentsList, true));
        //@! end chunk 5

        return Command::SUCCESS;
    }
}