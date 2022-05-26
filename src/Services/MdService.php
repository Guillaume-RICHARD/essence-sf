<?php

namespace App\Services;

class XmlService
{
    public $path = "data/essence/year/PrixCarburants_annuel_";
    public $format = ".xml";

    public function __construct($year) {
        $file = $this->path.$year.$this->format;

        if (file_exists($file)) {
            $xml = simplexml_load_file($file);
        } else {
            exit('Echec lors de l\'ouverture du fichier $file.');
        }

        return $xml->pdv;
    }
}