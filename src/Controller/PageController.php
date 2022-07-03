<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("/changelog", name="app_changelog")
     */
    public function changelog(): Response
    {
        $data = [];
        $content = '';

        $txt = new MdService('changelog');
        $string = (array)$txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');

        return $this->render('pages/changelog.html.twig', [
            'data' => $data,
            'text' => $content,
        ]);
    }

    /**
     * @Route("/a-propos", name="app_apropos")
     */
    public function apropos(): Response
    {
        $data = $article = [];
        $content = $previous = $next = '';

        $txt = new MdService('a-propos');
        $string = (array)$txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');

        return $this->render('pages/a-propos.html.twig', [
            'data' => $data,
            'text' => $content,
        ]);
    }
}
