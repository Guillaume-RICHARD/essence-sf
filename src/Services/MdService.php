<?php

namespace App\Services;

use Webuni\FrontMatter\FrontMatter;
use League\CommonMark\CommonMarkConverter;



class MdService
{

    public function __construct($page) {
        $this->file = __DIR__."/../../".$_SERVER['FILE_MD'].$page."/index.md";

        if (!file_exists($this->file)) {
            exit('Echec lors de l\'ouverture du fichier $markdown.');
        }
    }

    public function read() {
        $frontMatter = new FrontMatter();

        $handle = fopen($this->file, 'r');
        $markdown = fread($handle, filesize($this->file));

        $hasFrontMatter = $frontMatter->exists($markdown);
        if ($hasFrontMatter) {
            $document = $frontMatter->parse($markdown);
            $data = $document->getData();
            $content = $document->getContent();

            return [
                'data'    => $data,
                'content' => $content,
            ];
        }
    }
}