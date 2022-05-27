<?php

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        // echo "<pre>"; var_dump($articles); echo "</pre>"; die;

        return $this->render('blog/index.html.twig', [
            'data'      => $data,
            'text'      => $content,
            'articles'  => $articles
        ]);
    }
}
