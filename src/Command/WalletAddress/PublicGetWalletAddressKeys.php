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
 * Class PublicGetWalletAddressKeys
 * @package App\Command\WalletAddress
 *
 * This command is used to get the keys of a wallet address.
 * It outputs a list of keys objects.
 */

class PublicGetWalletAddressKeys extends Command
{
    protected static $defaultName = 'wa:get-keys';

    protected function configure(): void
    {
        $this
            ->setDescription('This command is used to get the keys of a wallet address.')
            ->setHelp('This command outputs a list of keys objects')
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

        //@! start chunk 3 | title=Get wallet address keys
        $walletKeys = $opClient->walletAddress()->getKeys([
            'url' => $config->getWalletAddressUrl()
        ]);
        //@! end chunk 3

        //@! start chunk 4 | title=Output wallet address keys
        $output->writeln('WALLET ADDRESS KEYS: '.print_r($walletKeys, true));
        //@! end chunk 4
       
        return Command::SUCCESS;
    }
}