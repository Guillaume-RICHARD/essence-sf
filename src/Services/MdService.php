<?php

namespace App\Services;

use Webuni\FrontMatter\FrontMatter;
use League\CommonMark\CommonMarkConverter;

class MdService
{
    /**
     * Recupère le fichier Markdown d'une page, si le fichiers existe
     * @param $page
     */
    public function __construct($page) {
        $this->dir = __DIR__."/../../".$_SERVER['FILE_MD'].$page;
        $this->file = $this->dir."/index.md";

        if (!file_exists($this->file)) {
            exit('Echec lors de l\'ouverture du fichier $markdown.');
        }
    }

    /**
     * Lit le contenu du fichier Markdown
     * @return array|void
     */
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

    /**
     * Lit le contenu de l'ensemble des Markdown d'un dossier
     * @return array
     */
    public function readAllArticles(int $nbArticle = 0) {
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
        $articles = array_reverse($articles);

        return ($nbArticle === 0) ? $articles : array_slice($articles, 0, 3);
    }

    /**
     * Lit le contenu d'un MarkDown Article
     * @param $id
     * @return array
     */
    public function readArticle($id) {
        $frontMatter = new FrontMatter();

        $infos = $article = [];

        $fileArticle = $this->dir."/articles/".$id."/index.md";

        $handle = fopen($this->dir."/articles/".$id."/index.md", 'r');
        $markdown = fread($handle, filesize($fileArticle));

        $this->folders = scandir($this->dir."/articles");
        array_splice($this->folders,0, 2);

        $hasFrontMatter = $frontMatter->exists($markdown);
        if ($hasFrontMatter) {
            $document = $frontMatter->parse($markdown);
            $article = [
                'data' => $document->getData(),
                'text' => $document->getContent()
            ];
        }

        foreach ($this->folders as $key => $folder) {
            if ($folder === $id) {
                $previous = array_key_exists($key - 1, $this->folders) ? $this->folders[$key -1] : false;
                $next = array_key_exists($key + 1, $this->folders) ? $this->folders[$key +1] : false;

                $infos = [
                    'article' => $article,
                    'previous' => $previous,
                    'next' => $next,
                ];
            }
        }

        return $infos;
    }
}