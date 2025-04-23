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
 * Class GrantOutgoingPayment
 * @package App\Command\Grant
 *
 * This command is used to generate an outgoing payment grant.
 * It outputs the access token value needed to make the outgoing payment request.
 */
class GrantOutgoingPayment extends Command
{
    protected static $defaultName = 'grant:op';

    protected function configure(): void
    {
        $this
            ->setDescription('Outputs an outgoing payment object, with the access_token value needed to make the outgoing payment request.')
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

        //@! start chunk 4 | title=Request outgoing payment grant
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
                                'receiver' => 'https://ilp.interledger-test.dev/incoming-payments/3eb5f240-82a7-40d7-bc10-b1ca833dbb2e',
                                'debitAmount'=> [
                                    'assetCode'=> 'USD',
                                    'assetScale'=> 2,
                                    'value'=> "132",
                                ]
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