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

        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);

        $wallet  = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        $grantRequest = [
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
        ];
        echo "Grant Request: ".print_r($grantRequest, true);
        $response = $opClient->grant()->request(
            [
                'url' => $wallet->authServer
            ],
            $grantRequest
        );

        $output->writeln('GRANT request response: '.print_r($response, true));
        $output->writeln('Please interact at the following URL: '.$response->interact->redirect);
        $output->writeln('CONTINUE_ACCESS_TOKEN = '.$response->continue->access_token->value);
        $output->writeln('CONTINUE_URI = '.$response->continue->uri);
        return Command::SUCCESS;
    }
}