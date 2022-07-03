<?php

declare(strict_types=1);

namespace App\Services\Xml;

class XmlService
{
    public string $flux;
    public string $path;
    public string $file;

    public function __construct(string $flux, string $infos = '')
    {
        // Inspiration : https://www.prix-carburants.gouv.fr/rubrique/opendata/
        // Intialisation des différentes variables pour la classe
        $url = '';
        $this->path = 'https://donnees.roulez-eco.fr/opendata/';
        $this->file = 'PrixCarburants_instantane.xml';
        $this->flux = $flux;

        switch ($this->flux) {
            case 'jour':
                $url = $this->path.'jour';
                break;
            case 'instantane':
                $url = $this->path.'instantane';
                break;
            case 'annee':
                $url = $this->path.'annee';
                if (!empty($infos)) {
                    $url .= $url.'/'.$infos;
                }
                break;
        }

        $fichier_nom = basename($url);
        $fichier_contenu = file_get_contents($url);

        if (file_put_contents(__DIR__.'/../../../'.$_ENV['FILE_DATA'].$flux.'/'.$fichier_nom.'.zip', $fichier_contenu)) {
            $zip = new \ZipArchive();

            $res = $zip->open(__DIR__.'/../../../'.$_ENV['FILE_DATA'].$flux.'/'.$fichier_nom.'.zip');

            if (true === $res) {
                $zip->extractTo(__DIR__.'/../../../'.$_ENV['FILE_DATA'].$flux.'/');
                $zip->close();
            // echo 'Fichier extrait avec succès dans le répertoire';
            } else {
                echo "Echec de l'extraction du fichier";
            }
        } else {
            echo 'Fichier non téléchargé';
        }
    }

    /**
     * @param array<int,mixed> $select
     *
     * @return array<int, array<string,mixed>>
     */
    public function request(array $select, int $limit = 10): array
    {
        if (file_exists(__DIR__.'/../../../'.$_ENV['FILE_DATA'].$this->flux.'/'.$this->file)) {
            $xml = simplexml_load_file(__DIR__.'/../../../'.$_ENV['FILE_DATA'].$this->flux.'/'.$this->file, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
            exit('Echec lors de l\'ouverture du fichier $file.');
        }

        $i = 0;
        $infos = [];

        if (!isset($xml->pdv) || empty($xml->pdv)) {
            exit();
        }
        foreach ($xml->pdv as $item) {
            /*
            echo '<pre>';
            var_dump($pdv['id'], $pdv['latitude']/100000, $pdv['longitude']/100000,  $pdv->adresse);
            echo '</pre>';
            die;
            */

            $tmp = [];
            if (in_array('id', $select)) {
                $tmp += [
                    'id' => $item['id'],
                ];
            }
            if (in_array('latitude', $select)) {
                $tmp += [
                    'latitude' => $item['latitude'],
                ];
            }
            if (in_array('longitude', $select)) {
                $tmp += [
                    'longitude' => $item['longitude'],
                ];
            }
            if (in_array('cp', $select)) {
                $tmp += [
                    'cp' => $item['cp'],
                ];
            }

            $infos[] = $tmp;

            ++$i;
            if ($i === $limit) {
                break;
            }
        }

        return $infos;
    }
}
