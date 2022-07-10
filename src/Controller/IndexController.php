<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        $data = [];
        $content = '';

        $txt = new MdService('accueil');
        $string = (array) $txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');
        $txt2 = new MdService('blog');
        $articles = $txt2->readAllArticles(4);

        return $this->render('index/index.html.twig', [
            'data' => $data,
            'text' => $content,
            'articles' => $articles,
        ]);
    }
}
