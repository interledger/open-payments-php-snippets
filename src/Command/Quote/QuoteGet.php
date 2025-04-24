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
 * Class QuoteGet
 * @package App\Command\Quote
 *
 * This command is used to get a quote.
 * It outputs the quote object.
 */

class QuoteGet extends Command
{
    protected static $defaultName = 'quote:get';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to get a quote.')
            ->setHelp('This command outputs the quote object')
            ->addArgument(
                'QUOTE_GRANT_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The access token for the quote.',
                $_ENV['QUOTE_GRANT_ACCESS_TOKEN'] ?? null
            )
            ->addArgument(
                'QUOTE_URL',
                InputArgument::OPTIONAL,
                'The url of the quote.',
                $_ENV['QUOTE_URL'] ?? ''
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];
        $QUOTE_GRANT_ACCESS_TOKEN = $input->getArgument('QUOTE_GRANT_ACCESS_TOKEN');
        $QUOTE_URL = $input->getArgument('QUOTE_URL');

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Get quote
        $quote = $opClient->quote()->get(
            [
                'access_token' => $QUOTE_GRANT_ACCESS_TOKEN,
                'url' => $QUOTE_URL
            ]
        );
        //@! end chunk 3
        //@! start chunk 4 | title=Output
        $output->writeln('QUOTE: '.print_r($quote, true));
        //@! end chunk 4
        return Command::SUCCESS;
    }
}