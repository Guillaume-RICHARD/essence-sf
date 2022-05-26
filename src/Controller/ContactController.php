<?php

namespace App\Controller;

use App\Services\MdService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function index(): Response
    {
        $txt    = new MdService('contact');
        $string  = $txt->read();
        extract($string, EXTR_PREFIX_SAME,"tdt");

        return $this->render('contact/index.html.twig', [
            'data'  => $data,
            'text'  => $content
        ]);
    }
}
