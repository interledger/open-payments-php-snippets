<?php
namespace App\Command\OutgoingPayment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1



/**
 * Class OutgoingPaymentList
 * @package App\Command\OutgoingPayment
 *
 * This command is used to list outgoing payments.
 * It outputs a list of outgoing payment objects.
 */

class OutgoingPaymentList extends Command
{
    protected static $defaultName = 'op:list';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to list outgoing payments.')
            ->setHelp('This command outputs a list of outgoing payment objects.')
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

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config(
            $WALLET_ADDRESS, $PRIVATE_KEY, $KEY_ID
        );
        $opClient = new AuthClient($config);
        //@! end chunk 2

        $wallet  = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);

        //@! start chunk 3 | title=List outgoing payments
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
        //@! end chunk 3

        //@! start chunk 5 | title=Output
        $output->writeln('OUTGOING PAYMENTS '.print_r($outgoingPaymentList, true));
        //@! end chunk 5

        return Command::SUCCESS;
    }
}