<?php

namespace App\Command\Grant;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1

/**
 * Class GrantIncomingPayment
 * @package App\Command\Grant
 *
 * This command is used to generate an incoming payment grant.
 * It outputs the access token value needed to make the incoming payment request.
 */
class GrantIncomingPayment extends Command
{
    protected static $defaultName = 'grant:ip';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an incoming payment object, with the access_token value needed to make the incoming payment request.')
            ->setHelp('This command allows you to receive an incoming payment grant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS,
            $PRIVATE_KEY,
            $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Get wallet address information
        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        //@! end chunk 3

        //@! start chunk 4 | title=Request incoming payment grant
        $grant = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            [
                'access_token' => [
                    'access' => [
                        [
                            'type' => 'incoming-payment',
                            'actions' => ['read', 'complete', 'create', 'list']
                        ]
                    ]
                ],
                'client' => $config->getWalletAddressUrl()
            ]
        );
        //@! end chunk 4

        //@! start chunk 5 | title=Check grant state
        if (!$grant instanceof \OpenPayments\Models\Grant) {
            throw new \Error('Expected non-interactive grant');
        }
        //@! end chunk 5

        $output->writeln('GRANT response: ' . print_r($grant, true));
        //@! start chunk 6 | title=Output
        $output->writeln('INCOMING_PAYMENT_GRANT: ' . $grant->access_token->value);
        $output->writeln("INCOMING_PAYMENT_ACCESS_TOKEN_MANAGE_URL = " . $grant->access_token->manage);
        //@! end chunk 6
        return Command::SUCCESS;
    }
}
