<?php
namespace App\Command\Quote;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use OpenPayments\AuthClient;
use OpenPayments\Config\Config;

class QuoteCreate extends Command
{
    protected static $defaultName = 'quote:create';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs a friendly greeting.')
            ->setHelp('This command allows you to output a greeting message...')
            ->addArgument(
                'QUOTE_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['QUOTE_GRANT_ACCESS_TOKEN'] ?? null) // Required argument
            ->addArgument(
                'INCOMING_PAYMENT_URL',
                InputArgument::OPTIONAL,
                'The name of the person to greet.',
                $_ENV['INCOMING_PAYMENT_URL']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $QUOTE_GRANT_ACCESS_TOKEN = $input->getArgument('QUOTE_GRANT_ACCESS_TOKEN');
        $INCOMING_PAYMENT_URL = $input->getArgument('INCOMING_PAYMENT_URL');
        $output->writeln('WALLET_ADDRESS: '.$WALLET_ADDRESS);
        $output->writeln('PRIVATE_KEY: '.$PRIVATE_KEY);
        $output->writeln('KEY_ID: '.$KEY_ID);
        $output->writeln('QUOTE_GRANT_ACCESS_TOKEN: '.$QUOTE_GRANT_ACCESS_TOKEN);
        $output->writeln('INCOMING_PAYMENT_URL: '.$INCOMING_PAYMENT_URL);

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        
        echo "wallet: ".print_r($wallet, true);
        
        $quoteRequest = [
            'method' => "ilp",
            'walletAddress'=> $wallet->id,
            'receiver'=> $INCOMING_PAYMENT_URL,
            'debitAmount' => [
                'assetCode' => 'USD',
                'assetScale' => 2,
                'value' => "132",
            ],
        ];
            
        $newOutgoingPayment = $opClient->quote()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $QUOTE_GRANT_ACCESS_TOKEN
            ],
            $quoteRequest
        );
        
        echo "CREATE_QUOTE request response: ".print_r($newOutgoingPayment, true);

        $output->writeln('QUOTE_URL: '.$newOutgoingPayment->id);

        return Command::SUCCESS;
    }
}