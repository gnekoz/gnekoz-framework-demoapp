<?php

namespace demo\report;

use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use demo\App;
use demo\db\DBChiamate;

/**
 *
 *
 * @author gneko
 */
class ReportBuilderChiamate
{
    private $from;

    private $to;

    public function __construct($from , $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function createReport()
    {
        $conf = App::getInstance()->getConfiguration();
        $templateFile = $conf->getProperty('/report/chiamate/template');

        $xls = PHPExcel_IOFactory::load($templateFile);

        $query = <<<EOT
        select
            c.*,
            u.nominativo as nome_destinatario
        from chiamate c
        left  join utenti u on u.id = c.id_utente_destinatario
        where
            c.immobile is not null
            and c.immobile != ''
            and date_trunc('day', c.data) >= '$this->from'
            and date_trunc('day', c.data) <= '$this->to'
        order by c.data ASC, u.nominativo ASC
EOT;

//        var_dump($query); exit();
        $chiamate = array();
        $chiamata = new DBChiamate();
        $chiamata->query($query);
        $row = 3;
        while ($chiamata->fetch())
        {
            // Clonazione da template
            $sheet = $xls->getSheetByName("chiamate");

            // Impostazione titolo
            $from = date('d/m/Y', strtotime($this->from));
            $to = date('d/m/Y', strtotime($this->to));
            $sheet->setCellValue("A1", "Chiamate dal {$from} al {$to}");

            // inserimento dettagli consumi
            $sheet->setCellValue("A$row", $chiamata->nome_destinatario);
            $sheet->setCellValue("B$row", PHPExcel_Shared_Date::PHPToExcel($chiamata->data));
            $sheet->setCellValue("C$row", $chiamata->pubblicita);
            $sheet->setCellValue("D$row", $chiamata->immobile);
            $sheet->setCellValue("E$row", $chiamata->nominativo_chiamante);
            $row++;
        }

        $conf = App::getInstance()->getConfiguration();
        $basePath = $conf->getProperty("/temp-dir") . DIRECTORY_SEPARATOR;

        $fileName = $basePath . uniqid("report_chiamate_") . ".xlsx";

        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->save($fileName);

        $xls->disconnectWorksheets();
        unset($xls);

        return $fileName;
    }
}

?>
