<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class OutgoingPaymentList extends Command
{
    protected static $defaultName = 'op:list';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN'] ?? null)
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN = $input->getArgument('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN');
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN: '.$OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        $walletService = $opClient->walletAddress();

        $wallet  = $walletService->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        
        $outgoingPaymentList = $opClient->outgoingPayment()->list(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $OUTGOING_PAYMENT_GRANT_ACCESS_TOKEN
            ],
            [
                'wallet-address' => $config->getWalletAddressUrl(),
                'first' => 3,
                'start'=> '96d964f0-3421-4df0-bb04-cb8d653bc571'
            ]
        );
        
        echo "OUTGOING response:<br><pre>".print_r($outgoingPaymentList, true)."</pre>";

       // $output->writeln('OUTGOING_PAYMENT_GRANT: '.$newOutgoingPayment->access_token->value);

        return Command::SUCCESS;
    }
}