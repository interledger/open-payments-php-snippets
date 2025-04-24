<?php
namespace App\Command\Quote;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1
/**
 * Class QuoteCreate
 * @package App\Command\Quote
 *
 * This command is used to create a quote.
 * It outputs the quote object.
 */
class QuoteCreate extends Command
{
    protected static $defaultName = 'quote:create';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to create a quote.')
            ->setHelp('This command outputs the quote url.')
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

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        
        //@! start chunk 3 | title=Create quote
        $newQuote = $opClient->quote()->create(
            [
                'url' => $wallet->resourceServer,
                'access_token' => $QUOTE_GRANT_ACCESS_TOKEN
            ],
            [
                'method' => "ilp",
                'walletAddress'=> $wallet->id,
                'receiver'=> $INCOMING_PAYMENT_URL,
                'debitAmount' => [
                    'assetCode' => 'USD',
                    'assetScale' => 2,
                    'value' => "130",
                ],
            ]
        );
        //@! end chunk 3

        $output->writeln('QUOTE '.print_r($newQuote, true));
        //@! start chunk 4 | title=Output
        $output->writeln('QUOTE_URL '.$newQuote->id);
        //@! end chunk 4
        return Command::SUCCESS;
    }
}