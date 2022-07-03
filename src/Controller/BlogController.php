<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(): Response
    {
        $data = [];
        $content = '';

        $txt = new MdService('blog');
        $string = (array) $txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');

        $articles = $txt->readAllArticles();

        return $this->render('blog/index.html.twig', [
            'data' => $data,
            'text' => $content,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/article/{id}", name="app_article")
     */
    public function articles(Request $request): Response
    {
        $data = $article = [];
        $content = $previous = $next = '';

        $txt = new MdService('blog');
        $route = (array) $request->attributes->get('_route_params');

        if (array_key_exists('id', $route)) {
            $string = $txt->readArticle((string) $route['id']);
            extract($string, EXTR_OVERWRITE, 'tdt');
        }

        return $this->render('blog/article.html.twig', [
            'article' => $article,
            'previous' => $previous,
            'next' => $next,
        ]);
    }
}
