<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\MdService;
use App\Services\Xml\XmlService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartographieController extends AbstractController
{
    /**
     * @Route("/cartographie", name="app_cartographie")
     */
    public function index(): Response
    {
        $data = [];
        $content = '';

        $txt = new MdService('cartographie');
        $string = (array)$txt->read();
        extract($string, EXTR_OVERWRITE, 'tdt');

        $select = ['id', 'latitude', 'longitude', 'cp'];
        $limit = 1;
        $xml = (new XmlService('instantane'))->request($select);
        // var_dump($xml); die;

        return $this->render('cartographie/index.html.twig', [
            'data' => $data,
            'text' => $content,
        ]);
    }
}
