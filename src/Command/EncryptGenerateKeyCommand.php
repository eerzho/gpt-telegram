<?php

namespace App\Command;

use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:encrypt:generate-key',
    description: 'Add a short description for your command',
    aliases: ['e:generate-key']
)]
class EncryptGenerateKeyCommand extends Command
{
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $isSave = KeyFactory::save(
            KeyFactory::generateEncryptionKey(),
            $this->parameterBag->get('app.encryption.key_path')
        );

        if ($isSave) {
            $io->success('Key created');

            return Command::SUCCESS;
        } else {
            $io->error('Something went wrong');

            return Command::FAILURE;
        }
    }
}
