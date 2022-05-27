<?php

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
        $txt    = new MdService('accueil');
        $string  = $txt->read();
        extract($string, EXTR_PREFIX_SAME,"tdt");

        return $this->render("index/index.html.twig", [
            'data'  => $data,
            'text'  => $content
        ]);
    }
}
