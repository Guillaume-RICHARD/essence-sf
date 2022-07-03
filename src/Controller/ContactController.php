<?php

declare(strict_types=1);

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
        $data = [];
        $content = '';

        $txt = new MdService('contact');
        $string = (array)$txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');

        return $this->render('contact/index.html.twig', [
            'data' => $data,
            'text' => $content,
        ]);
    }
}
