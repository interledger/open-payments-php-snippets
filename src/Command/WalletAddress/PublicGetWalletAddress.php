<?php
namespace App\Command\WalletAddress;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//@! start chunk 1 | title=Import dependencies
use OpenPayments\AuthClient;
use OpenPayments\Config\Config;
//@! end chunk 1

/**
 * Class PublicGetWalletAddress
 * @package App\Command\WalletAddress
 *
 * This command is used to get a wallet address.
 * It outputs the wallet address object.
 */

class PublicGetWalletAddress extends Command
{
    protected static $defaultName = 'wa:get';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to get a wallet address.')
            ->setHelp('This command outputs the wallet address object.')
            ->addArgument(
                'WALLET_ADDRESS',
                InputArgument::OPTIONAL,
                'The wallet address url.',
                $_ENV['WALLET_ADDRESS'] ?? null
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $WALLET_ADDRESS = $input->getArgument('WALLET_ADDRESS');

        //@! start chunk 2 | title=Initialize Open Payments client
        $config = new Config($WALLET_ADDRESS);
        $opClient = new AuthClient($config);
        //@! end chunk 2

        //@! start chunk 3 | title=Get wallet address
        $wallet = $opClient->walletAddress()->get([
            'url' => $config->getWalletAddressUrl()
        ]);
        //@! end chunk 3

        //@! start chunk 4 | title=Output wallet address
        $output->writeln('WALLET ADDRESS '.print_r($wallet, true));
        //@! end chunk 4

        return Command::SUCCESS;
    }
}