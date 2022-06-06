<?php

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PageController extends AbstractController
{
    /**
     * @Route("/changelog", name="app_changelog")
     */
    public function changelog(): Response
    {
        $txt    = new MdService('changelog');
        $string  = $txt->read();
        extract($string, EXTR_PREFIX_SAME,"tdt");

        return $this->render('pages/changelog.html.twig', [
            'data'      => $data,
            'text'      => $content
        ]);
    }

    /**
     * @Route("/a-propos", name="app_apropos")
     */
    public function apropos(): Response
    {
        $txt    = new MdService('a-propos');
        $string  = $txt->read();
        extract($string, EXTR_PREFIX_SAME,"tdt");

        return $this->render('pages/a-propos.html.twig', [
            'data'      => $data,
            'text'      => $content
        ]);
    }
}
