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
 * Class GrantOutgoingPaymentInterval
 * @package App\Command\Grant
 *
 * This command is used to generate an outgoing payment grant with a specific interval.
 * It outputs the access token value needed to make the outgoing payment request.
 */

class GrantOutgoingPaymentInterval extends Command
{
    protected static $defaultName = 'grant:interval';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an outgoing payment object, with the access_token value needed to make the ougoing payment request.')
            ->setHelp('This command allows you to receive an outgoing payment grant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS =  $_ENV['WALLET_ADDRESS'];
        $PRIVATE_KEY = $_ENV['PRIVATE_KEY'];
        $KEY_ID = $_ENV['KEY_ID'];

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Get wallet address information
        $wallet  = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        //@! end chunk 3

        //@! start chunk 4 | title=Request outgoing payment grant with interval
        $grant = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            [
                'access_token' => [
                    'access' => [
                            [
                                'type' => 'outgoing-payment',
                                'actions' => ['list', 'list-all', 'read', 'read-all','create'],
                                'identifier'=> $wallet->id,
                                'limits' => [
                                    'debitAmount'=> [
                                        'assetCode'=> 'USD',
                                        'assetScale'=> 2,
                                        'value'=> "132",
                                    ],
                                    'interval' => 'R/2025-04-22T08:00:00Z/P1D',
                                ],
                            ]
                        ]
                ],
                'client' => $config->getWalletAddressUrl(),
                'interact'=> [
                    'start'=> ["redirect"],
                    'finish'=>[
                      'method'=> "redirect",
                      'uri'=> 'https://localhost/?paymentId=123423',
                      'nonce'=> "1234567890",
                    ],
                ]
            ]
        );
        //@! end chunk 4

        //@! start chunk 5 | title=Check grant state
        if(!$grant?->interact) {
            throw new \Error('Expected interactive grant');
        }
        // OR
        if(!$grant instanceof \OpenPayments\Models\PendingGrant) {
            throw new \Error('Expected interactive grant');
        }
        //@! end chunk 5

        $output->writeln('GRANT request response: '.print_r($grant, true));
        //@! start chunk 6 | title=Output
        $output->writeln('Please interact at the following URL: '.$grant->interact->redirect);
        $output->writeln('CONTINUE_ACCESS_TOKEN = '.$grant->continue->access_token->value);
        $output->writeln('CONTINUE_URI = '.$grant->continue->uri);
        //@! end chunk 6

        return Command::SUCCESS;
    }
}