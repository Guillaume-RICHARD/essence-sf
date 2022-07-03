<?php

declare(strict_types=1);

namespace App\Services;

use Webuni\FrontMatter\FrontMatter;

class MdService
{
    public string $dir = '';
    public string $file = '';

    /**
     * Recupère le fichier Markdown d'une page, si le fichiers existe.
     *
     * @param $page
     */
    public function __construct(string $page)
    {
        if (is_dir(__DIR__.'/../../'.$_SERVER['FILE_MD'].$page)) {
            $this->dir = __DIR__.'/../../'.$_SERVER['FILE_MD'].$page;
            $this->file = $this->dir.'/index.md';
        }
    }

    /**
     * Lit le contenu du fichier Markdown.
     *
     * @return array<string, array<string, mixed>|string>
     */
    public function read(): array
    {
        $data = [];
        $content = [];

        $frontMatter = new FrontMatter();

        // $handle = '';
        if (($handle = fopen($this->file, 'r')) && filesize($this->file)) {
            $markdown = fread($handle, filesize($this->file));

            $hasFrontMatter = $frontMatter->exists((string) $markdown);
            if ($hasFrontMatter) {
                $document = $frontMatter->parse((string) $markdown);
                $data = $document->getData();
                $content = $document->getContent();
            }
        }

        return [
            'data' => $data,
            'content' => $content,
        ];
    }

    /**
     * Lit le contenu de l'ensemble des Markdown d'un dossier.
     *
     * @return array<int, array<string,mixed>>
     */
    public function readAllArticles(int $nbArticle = 0): array
    {
        $frontMatter = new FrontMatter();

        if (!is_array(scandir($this->dir.'/articles'))) {
            exit("Pas d'articles existant !");
        }

        $folders = scandir($this->dir.'/articles');
        array_splice($folders, 0, 2);

        $articles = [];
        foreach ($folders as $folder) {
            $md = $this->dir.'/articles/'.$folder.'/index.md';
            if (($handle = fopen($md, 'r')) && filesize($this->file)) {
                $markdown = fread($handle, filesize($this->file));

                $hasFrontMatter = $frontMatter->exists((string) $markdown);
                if ($hasFrontMatter) {
                    $document = $frontMatter->parse((string) $markdown);
                    $articles[] = [
                        'data' => $document->getData(),
                        'content' => $document->getContent(),
                    ];
                }
            }
        }
        $articles = array_reverse($articles);

        return (0 === $nbArticle) ? $articles : \array_slice($articles, 0, 3);
    }

    /**
     * Lit le contenu d'un MarkDown Article.
     *
     * @return array<string, array<string, array<string, mixed>|string>|string>
     */
    public function readArticle(string $id): array
    {
        $frontMatter = new FrontMatter();

        $markdown = '';
        $infos = $article = [];

        $fileArticle = $this->dir.'/articles/'.$id.'/index.md';

        $md = $this->dir.'/articles/'.$id.'/index.md';
        if (($handle = fopen($md, 'r')) && filesize($fileArticle)) {
            // $handle = fopen($this->dir.'/articles/'.$id.'/index.md', 'r');
            $markdown = fread($handle, filesize($fileArticle));
        }

        if (!is_array(scandir($this->dir.'/articles'))) {
            exit("Pas d'articles existant !");
        }

        $folders = scandir($this->dir.'/articles');
        array_splice($folders, 0, 2);

        $hasFrontMatter = $frontMatter->exists((string) $markdown);
        if ($hasFrontMatter) {
            $document = $frontMatter->parse((string) $markdown);
            $article = [
                'data' => $document->getData(),
                'text' => $document->getContent(),
            ];
        }

        if (\in_array($id, $folders)) {
            $key = array_search($id, $folders);

            $previous = \array_key_exists($key - 1, $folders) ? $folders[$key - 1] : [];
            $next = \array_key_exists($key + 1, $folders) ? $folders[$key + 1] : [];


            $infos = [
                'article' => $article,
                'previous' => $previous,
                'next' => $next,
            ];
        }

        return $infos;
    }
}
