<?php

namespace App\Services;

use Webuni\FrontMatter\FrontMatter;
use League\CommonMark\CommonMarkConverter;

class MdService
{
    public function __construct($page) {
        $this->dir = __DIR__."/../../".$_SERVER['FILE_MD'].$page;
        $this->file = $this->dir."/index.md";

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

    public function readAllArticles() {
        $frontMatter = new FrontMatter();

        $this->folders = scandir($this->dir."/articles");
        array_splice($this->folders,0, 2);

        $articles = [];
        foreach ($this->folders as $folder) {
            $handle = fopen($this->dir."/articles/".$folder."/index.md", 'r');
            $markdown = fread($handle, filesize($this->file));

            $hasFrontMatter = $frontMatter->exists($markdown);
            if ($hasFrontMatter) {
                $document = $frontMatter->parse($markdown);
                $articles[] = [
                    'data' => $document->getData(),
                    'content' => $document->getContent()
                ];
            }
        }

        return $articles;
    }
}