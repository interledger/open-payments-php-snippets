<?php

namespace App\Command\Grant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1

/**
 * Class GrantContinuation
 * @package App\Command\Grant
 *
 * This command is used to generate an outgoing payment grant.
 * It outputs the access token value needed to make the outgoing payment request.
 */
class GrantContinuation extends Command
{
    protected static $defaultName = 'grant:continuation';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an outgoing payment object, with the access_token value needed to make the outgoing payment request.')
            ->setHelp('This command allows you to receive an outgoing payment grant')
            ->addArgument(
                'CONTINUE_ACCESS_TOKEN',
                InputArgument::OPTIONAL,
                'The value of CONTINUE_ACCESS_TOKEN',
                $_ENV['CONTINUE_ACCESS_TOKEN']
            )
            ->addArgument(
                'URL_WITH_INTERACT_REF',
                InputArgument::OPTIONAL,
                'The value of URL_WITH_INTERACT_REF',
                $_ENV['URL_WITH_INTERACT_REF']
            )
            ->addArgument(
                'CONTINUE_URI',
                InputArgument::OPTIONAL,
                'The value of CONTINUE_URI',
                $_ENV['CONTINUE_URI']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];


        $CONTINUE_ACCESS_TOKEN = $input->getArgument('CONTINUE_ACCESS_TOKEN');
        $URL_WITH_INTERACT_REF = $input->getArgument('URL_WITH_INTERACT_REF');
        $CONTINUE_URI = $input->getArgument('CONTINUE_URI');
        $parse = parse_url($URL_WITH_INTERACT_REF);
        $query = $parse['query'] ?? '';
        parse_str($query, $params);
        $interactRef = $params['interact_ref'] ?? null;

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS,
            $PRIVATE_KEY,
            $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Continue grant
        $grant = $opClient->grant()->continue(
            [
                'access_token' => $CONTINUE_ACCESS_TOKEN,
                'url' => $CONTINUE_URI
            ],
            [
                'interact_ref' => $interactRef,
            ]
        );
        //@! end chunk 3

        //@! start chunk 4 | title=Check grant state
        if (!$grant instanceof \OpenPayments\Models\Grant) {
            throw new \Error('Expected finalized grant. Received non-finalized grant.');
        }
        //@! start chunk 4 | title=Check grant state

        $output->writeln('GRANT request response: ' . print_r($grant, true));
        //@! start chunk 5 | title=Output
        $output->writeln('OUTGOING_PAYMENT_GRANT_ACCES_TOKEN: ' . $grant->access_token->value);
        $output->writeln('OUTGOING_PAYMENT_ACCESS_TOKEN_MANAGE_URL: ' . $grant->access_token->manage);
        //@! end chunk 5
        return Command::SUCCESS;
    }
}
