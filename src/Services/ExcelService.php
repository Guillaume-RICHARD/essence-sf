<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelService
{
    public function __construct() {}

    /**
     * @param $data
     * @param $nomFichier
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function generation_excel($data, $nomFichier)
    {
        extract($data, EXTR_PREFIX_SAME,"tdt");
        /**
         * création des variables suivantes :
         *      $nb_tours, $nb_tables, $nb_places
         *      $list_tables, $list_participant
         *      $rencontre, $non_rencontre
         *      $numeros_speciaux, $tour_de_table
         */
        $nb_participant = count($list_participant);

        $spreadsheet = new Spreadsheet();
        $myWorkSheet = $spreadsheet->getActiveSheet();

        //////////////////////////
        // Onglet 1
        //////////////////////////
        $spreadsheet->getSheet(0);
        $myWorkSheet->setTitle("Configuration");
        $myWorkSheet->getColumnDimension('A')->setWidth(220, 'px');
        $myWorkSheet->getColumnDimension('B')->setWidth(150, 'px');
        $myWorkSheet->getColumnDimension('C')->setWidth(120, 'px');
        $myWorkSheet->getColumnDimension('D')->setWidth(150, 'px');

        $myWorkSheet->setCellValue('A1', 'CONFIGURATION');
        $myWorkSheet->setCellValue('A3', 'Participants');
        $myWorkSheet->setCellValue('A4', 'Tours');
        $myWorkSheet->setCellValue('A5', 'Logistique');
        $myWorkSheet->setCellValue('A6', 'Nombres de rencontres produites');

        $myWorkSheet->setCellValue('B3', $nb_participant);
        $myWorkSheet->setCellValue('B4', $nb_tours);
        $myWorkSheet->setCellValue('B5', $nb_tables." tables de ".$nb_places);
        $myWorkSheet->setCellValue('B6', $nb_participant * $nb_tours * $nb_tables);

        // Tables utilisées & Numéros spéciaux
        $myWorkSheet->setCellValue('A8', 'Tables utilisées');
        $myWorkSheet->setCellValue('C8', 'Numéros spéciaux');

        $row_table = $row_numeros_spe = 7;
        foreach ($list_tables as $table) {
            $row_table++;
            $myWorkSheet->setCellValue('B'.$row_table, $table);
        }

        foreach ($numeros_speciaux as $numero_spe) {
            $row_numeros_spe++;
            $myWorkSheet->setCellValue('D'.$row_numeros_spe, $numero_spe);
        }

        // vérification du nombres de tables & Numéros spéciaux
        if ($row_table >= $row_numeros_spe) {
            $cell = $row_table;
        } else if ($row_table < $row_numeros_spe) {
            $cell = $row_numeros_spe;
        }

        // Récupérer la valeur max des index des $rencontre & $norencontre
        if (max(array_keys($rencontre)) >= max(array_keys($non_rencontre))) {
            $format = max(array_keys($rencontre));
        } else if (max(array_keys($rencontre)) < max(array_keys($non_rencontre))) {
            $format = max(array_keys($non_rencontre));
        }

        // Gestions des rencontres/non-rencontres, des différents tours de tables
        for ($i = 1; $i <= $format; $i++) {
            $myWorkSheet->getCellByColumnAndRow(1, $cell+2)->setValue('Se rencontre')->getStyle()->applyFromArray($this->style_excel("bold"));
            $myWorkSheet->getCellByColumnAndRow(1, $cell+3)->setValue('Ne se rencontre pas')->getStyle()->applyFromArray($this->style_excel("bold"));

            if (isset($rencontre[$i]) && !empty($rencontre[$i])) {
                foreach ($rencontre[$i] as $key => $r) {
                    $myWorkSheet->getCellByColumnAndRow($key+2, $cell+2)->setValue("[".implode(",", $r)."]");
                }
            }

            if (isset($non_rencontre[$i]) && !empty($non_rencontre[$i])) {
                foreach ($non_rencontre[$i] as $key => $r) {
                    $myWorkSheet->getCellByColumnAndRow($key+2, $cell+3)->setValue("[".implode(",", $r)."]");
                }
            }
            $cell = $cell + 3;
        }

        // Style pour l'onglet 1
        $arr = ['B3', 'B4', 'B5', 'B6'];
        foreach ($arr as $cell) {
            foreach ($arr as $cell) {
                $myWorkSheet->getStyle($cell)->applyFromArray($this->style_excel("alignment"));
            }
        }

        $arr = ['A1', 'A3', 'A4', 'A5', 'A6', 'A8', 'C8'];
        foreach ($arr as $cell) {
            $myWorkSheet->getStyle($cell)->applyFromArray($this->style_excel("bold"));
        }

        //////////////////////////
        // Onglet 2
        //////////////////////////
        $myWorkSheet1 = new Worksheet($spreadsheet, 'Tableaux des rencontres');
        $spreadsheet->addSheet($myWorkSheet1, 1);

        // Génération dynamique d'un tableau en rapport avec le nombre de participants
        for ($i = 1; $i <= $nb_participant; ++$i)
            $arr_table[$i] = "N°".$i;

        // Setting basique pour l'onglet 2
        $myWorkSheet1->getColumnDimension('A')->setWidth(120, 'px');
        $myWorkSheet1->getColumnDimension('B')->setVisible(false);
        $myWorkSheet1->setCellValue('A1', 'Tables');
        $myWorkSheet1->getStyle('A1')->applyFromArray($this->style_excel("grey_bold"));

        // Setting pour la première colonne
        for ($row = 1; $row <= $nb_participant; ++$row) {
            $myWorkSheet1->setCellValue('A'.($row+1), $arr_table[$row]);
            $myWorkSheet1->getCellByColumnAndRow(1, ($row+1))
                ->getStyle()
                ->applyFromArray($this->style_excel("grey_bold"));

            if (in_array($row, $numeros_speciaux)) {
                $myWorkSheet1->getCellByColumnAndRow(1, ($row+1))
                    ->setValue($arr_table[$row])
                    ->getStyle()->applyFromArray($this->style_excel("red_background"));
            }
        }

        // Setting pour la première ligne
        for ($col = 3; $col <= ($nb_participant + 2); ++$col) {
            $myWorkSheet1->getCellByColumnAndRow($col, 1)
                ->setValue($arr_table[$col-2])->getStyle()
                ->applyFromArray($this->style_excel("grey_bold"))
                ->applyFromArray($this->style_excel("text_rotation"));

            if (in_array($col-2, $numeros_speciaux)) {
                $myWorkSheet1->getCellByColumnAndRow($col, 1)
                    ->setValue($arr_table[$col-2])
                    ->getStyle()->applyFromArray($this->style_excel("red_background"));
            }

            for ($row = 0; $row <= ($nb_participant + 1); ++$row) {
                $myWorkSheet1->getCellByColumnAndRow($col, $row)
                    ->getStyle()->applyFromArray($this->style_excel("border"));

                if ($row === ($col - 1)) {
                    $myWorkSheet1->getCellByColumnAndRow($col, $row)
                        ->getStyle()->applyFromArray($this->style_excel("fill"));
                }
            }
        }
        $myWorkSheet1->getRowDimension('1')->setRowHeight(50);

        // On ajoute les infos du tableau de rencontre.
        $infos_rencontre_data = $this->tableau_rencontre($tour_de_table, $nb_participant);
        $myWorkSheet1->fromArray($infos_rencontre_data,NULL,'C2');


        foreach (range('C', $myWorkSheet1->getHighestDataColumn()) as $col) {
            $myWorkSheet1->getColumnDimension($col)->setWidth(25, 'px');
        }

        //////////////////////////
        // Onglet 3
        //////////////////////////
        $myWorkSheet2 = new Worksheet($spreadsheet, 'Plans de tables');
        $spreadsheet->addSheet($myWorkSheet2, 2);

        $myWorkSheet2->setCellValue('A1', 'PARTICIPANTS');
        $myWorkSheet2->getColumnDimension('A')->setWidth(120, 'px');
        $myWorkSheet2->getRowDimension('1')->setRowHeight(50);


        // 1ere colonne
        $ligne = 1;
        foreach ($list_participant as $participant) {
            $ligne++;
            $myWorkSheet2->setCellValue('A'.$ligne, "N°".$participant);
        }
        // style des cases
        $lastRow = $myWorkSheet2->getHighestRow();
        for ($i=0; $i <= $lastRow; $i++ ) {
            $myWorkSheet2->getStyle('A'.$i)
                ->applyFromArray($this->style_excel("grey_bold"));

            if (in_array($i-1, $numeros_speciaux)) {
                $myWorkSheet2->getCellByColumnAndRow(1, $i)
                    ->getStyle()->applyFromArray($this->style_excel("red_background"));
            }
        }

        // 1ere ligne
        for ($col=1; $col <= $nb_tours; $col++) {
            $myWorkSheet2->getCellByColumnAndRow($col+1, 1)
                ->setValue("Tour N°".$col)
                ->getStyle()
                ->applyFromArray($this->style_excel("grey_bold"));
        }

        $lastCol = $myWorkSheet2->getHighestColumn();
        $myWorkSheet2->getStyle('B2:'.$lastCol.$lastRow)
            ->applyFromArray($this->style_excel("all_borders"));

        // Todo : Ajout Fonction plan de table
        $plan_de_table = $this->plan_table($tour_de_table);
        $myWorkSheet2->fromArray($plan_de_table,NULL,'B2');

        foreach (range('A', $myWorkSheet2->getHighestDataColumn()) as $col) {
            $myWorkSheet2->getStyle($col)->getAlignment()->setHorizontal('center');
            $myWorkSheet2->getStyle($col)->getAlignment()->setVertical('center');
            $myWorkSheet2->getColumnDimension($col)->setWidth(120, 'px');
        }

        //////////////////////////
        // Onglet 4
        //////////////////////////
        $myWorkSheet3 = new Worksheet($spreadsheet, 'Placement');
        $spreadsheet->addSheet($myWorkSheet3, 3);

        $myWorkSheet3->setCellValue('A1', 'TABLE');
        $myWorkSheet3->getColumnDimension('A')->setWidth(120, 'px');
        $myWorkSheet3->getRowDimension('1')->setRowHeight(50);

        // 1ere colonne
        $ligne = 1;
        foreach ($list_tables as $table) {
            $ligne++;
            $myWorkSheet3->setCellValue('A'.$ligne, $table);
        }
        // style des cases
        $lastRow = $myWorkSheet3->getHighestRow();
        for ($i=0; $i <= $lastRow; $i++ ) {
            $myWorkSheet3->getStyle('A'.$i)
                ->applyFromArray($this->style_excel("grey_bold"));
        }

        // 1ere ligne
        for ($col=1; $col <= $nb_tours; $col++) {
            $myWorkSheet3->getCellByColumnAndRow($col+1, 1)
                ->setValue("Tour N°".$col)
                ->getStyle()
                ->applyFromArray($this->style_excel("grey_bold"));
        }

        $lastCol = $myWorkSheet3->getHighestColumn();
        $myWorkSheet3->fromArray($this->ajout_num($tour_de_table),NULL,'B2');

        $myWorkSheet3->getStyle('B2:'.$lastCol.$lastRow)
            ->applyFromArray($this->style_excel("all_borders"));

        foreach (range('A', $myWorkSheet3->getHighestDataColumn()) as $col) {
            $myWorkSheet3->getStyle($col)->getAlignment()->setHorizontal('center');
            $myWorkSheet3->getStyle($col)->getAlignment()->setVertical('center');
            $myWorkSheet3->getColumnDimension($col)->setAutoSize(true);
        }


        // Téléchargement du fichier Excel
        $writer = new Xlsx($spreadsheet); // Create your Office 2007 Excel (XLSX Format)

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($nomFichier).'"');
        $writer->save($nomFichier); // Create the file
        return $writer->save('php://output'); // Create the file
    }

    /**
     * traitement des données envoyé au fichier Excel
     * @param $data
     * @return array
     */
    public function data_traitement($infos) {
        $tour_de_table    = $infos["tour_de_table"];
        $rencontre        = $infos["rencontre"];
        $non_rencontre    = $infos["non_rencontre"];
        $numeros_speciaux = $infos["numero_speciaux"][0];

        $nb_tours   = count($tour_de_table);
        $nb_tables  = count($tour_de_table[0]);
        $nb_places  = count($tour_de_table[0][0]);

        $participants = $list_tables = [];
        foreach ($tour_de_table as $key => $info) {
            $list_tables[] = "Table N°".((int)$key+1);
            foreach ($info as $p) {
                $participants =  array_merge($participants, $p);
            }
        }

        $list_participant = array_unique($participants);
        sort($list_participant);

        return [
            'nb_tours'          => $nb_tours,
            'nb_tables'         => $nb_tables,
            'nb_places'         => $nb_places,
            'list_tables'       => $list_tables,
            'list_participant'  => $list_participant,
            'rencontre'         => $rencontre,
            'non_rencontre'     => $non_rencontre,
            'numeros_speciaux'  => $numeros_speciaux,
            'tour_de_table'     => $tour_de_table
        ];
    }

    /**
     * Génération du tableau de rencontre (Onglet 2)
     * @param $tour_de_table
     * @param $nb_participant
     * @return array
     */
    private function tableau_rencontre($tour_de_table, $nb_participant):array
    {
        $tableau_de_rencontre = [];
        for ($i = 1; $i <= $nb_participant; $i++) {
            $tableau_de_rencontre[$i] = array_fill(1, $nb_participant, "");
        }
        foreach ($tour_de_table as $key_tour => $tour_par_tour) {
            foreach ($tour_par_tour as $key => $utilisateurs_par_table) {
                foreach ($utilisateurs_par_table as $key => $utilisateur) {
                    if(isset($utilisateurs_par_table[$key+1])) {
                        $tableau_de_rencontre[$utilisateur][$utilisateurs_par_table[$key+1]] = 1;
                        $tableau_de_rencontre[$utilisateurs_par_table[$key+1]][$utilisateur] = 1;
                    }
                }
            }
        }
        ksort($tableau_de_rencontre);

        return $tableau_de_rencontre;
    }

    /**
     * Génération du plan de table (Onglet 3)
     * @param $tour_de_table
     * @return array
     */
    private function plan_table($tour_de_table):array
    {
        // Ligne : $participants // Colonne : $tours
        $plan_de_table = [];
        foreach ($tour_de_table as $key_tour => $tour_par_tour) {
            foreach ($tour_par_tour as $key => $utilisateurs_par_table) {
                foreach ($utilisateurs_par_table as $utilisateurs) {
                    $plan_de_table[$utilisateurs][$key_tour] = "Table N°".($key+1);
                }
            }
        }
        ksort($plan_de_table);

        return $plan_de_table;
    }

    /**
     * Gestions des différents styles Excel
     * @param $styles
     * @return array|array[]|\array[][]|\bool[][]
     */
    private function style_excel($styles) {
        switch ($styles) {
            case "bold":
                $style = [
                    'font' => [
                        'bold' => true,
                    ],
                ];
                break;

            case "grey_bold":
                $style = [
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFd9d9d9',
                        ],
                    ],
                ];
                break;

            case "red_background":
                $style = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFf8cbad',
                        ],
                    ],
                ];
                break;

            case "border":
                $style = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ];
                break;

            case "all_borders":
                $style = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                        ]
                    ],
                ];
                break;

            case "fill":
                $style = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF000000',
                        ],
                    ],
                ];
                break;

            case "alignment":
                $style = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ];
                break;

            case "text_rotation":
                $style = [
                    'alignment' => [
                        'textRotation' => 90,
                    ],
                ];

                break;
        }

        return $style;
    }

    /**
     * Ajout du préfixe "N°" devant certains numéros.
     * @param $infos
     * @return mixed
     */
    private function ajout_num($infos) {
        foreach ($infos as &$info) {
            foreach ($info as &$elements) {
                foreach ($elements as &$element) {
                    $element = "N°".$element;
                }
                $elements = implode(", ", $elements);
            }
        }
        return $infos;
    }
}