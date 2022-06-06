<?php

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="app_blog")
     */
    public function index(): Response
    {
        $txt    = new MdService('blog');
        $string  = $txt->read();
        extract($string, EXTR_PREFIX_SAME,"tdt");

        $articles  = $txt->readAllArticles();

        return $this->render('blog/index.html.twig', [
            'data'      => $data,
            'text'      => $content,
            'articles'  => $articles
        ]);
    }

    /**
     * @Route("/blog/article/{id}", name="app_article")
     */
    public function articles(Request $request): Response
    {
        $txt    = new MdService('blog');
        $route  = $request->attributes->get('_route_params');

        if ($route['id'] ) {
            $string  = $txt->readArticle($route['id']);
            extract($string, EXTR_PREFIX_SAME,"tdt");

            return $this->render('blog/article.html.twig', [
                'article' => $article,
                'previous' => $previous,
                'next' => $next,
            ]);
        }
    }
}
