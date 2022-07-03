<?php

declare(strict_types=1);

// src/Command/CreateUserCommand.php
// https://symfony.com/doc/current/console.html

namespace App\Command;

use Faker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateArticleCommand extends Command
{
    public string $dir = '';
    public int $content = 0;

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-content';

    // Configuring the Command ...
    protected function configure(): void
    {
        // configure an argument
        $this->addArgument('content', InputArgument::REQUIRED, 'Number of Article.');

        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command allows you to create content for Article...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Faker\Factory::create('fr_FR');

        try {
            $this->content = \intval($input->getArgument('content')); // transformation obligatoire en Int
        } catch (\Exception $e) {
            var_dump(Command::INVALID);
        }

        if (0 === $this->content) {
            return Command::INVALID;
        }

        // Se placer dans le dossier public/pages/blog/articles
        $this->dir = __DIR__.'/../../'.$_SERVER['FILE_MD'].'blog/articles/';

        for ($i = 0; $i < $this->content; ++$i) {
            // Créer dossier sous la forme YYYYMMDD-HHMMSS
            $path = $faker->date('Ymd-Hi');
            $desc = $faker->text();
            $lorem = $faker->paragraph();
            $word = $faker->word();

            $data = '---'.PHP_EOL;
            $data .= 'id: '.$path.PHP_EOL;
            $data .= 'layout: Blog'.PHP_EOL;
            $data .= 'title: News du '.$path.PHP_EOL;
            $data .= 'description: '.$desc.PHP_EOL;
            $data .= 'tags: '.PHP_EOL;
            $data .= '- '.$word.PHP_EOL;
            $data .= '- '.$word.PHP_EOL;
            $data .= '---'.PHP_EOL;
            $data .= ''.PHP_EOL;
            $data .= '## News du '.$path.PHP_EOL;
            $data .= ''.PHP_EOL;
            $data .= '![excited-spin](/img/cartoon-cat/excited-spin.gif)'.PHP_EOL;
            $data .= ''.PHP_EOL;
            $data .= $lorem.PHP_EOL;
            $data .= $lorem.PHP_EOL;
            $data .= $lorem.PHP_EOL;
            $data .= $lorem.PHP_EOL;

            mkdir($this->dir.$path, 0777);
            file_put_contents($this->dir.$path.'/index.md', $data);
        }

        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
    }
}
