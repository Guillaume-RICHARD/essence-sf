<?php

// src/Command/CreateUserCommand.php
// https://symfony.com/doc/current/console.html

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteArticleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:delete-content';

    // Configuring the Command ...
    protected function configure(): void
    {
        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command allows you to create content for Article...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Se placer dans le dossier public/pages/blog/articles
        $this->dir = __DIR__."/../../".$_SERVER['FILE_MD']."blog/articles/";

        $this->deleteTree($this->dir); // On vide le contenu de notre dossier
        // rmdir($this->dir); // Et on le supprime

        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
    }

    protected function deleteTree($dir){
        foreach(glob($dir . "/*") as $element){
            if(is_dir($element)){
                $this->deleteTree($element); // On rappel la fonction deleteTree
                rmdir($element); // Une fois le dossier courant vidé, on le supprime
            } else { // Sinon c'est un fichier, on le supprime
                unlink($element);
            }
            // On passe à l'élément suivant
        }
    }
}