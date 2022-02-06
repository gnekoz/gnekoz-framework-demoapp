<?php

namespace demo\report;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use demo\App;
use demo\db\DBContatti;

/**
 *
 *
 * @author Luca Stauble
 */
class ReportBuilderContatti
{
    const REPORT_CONSULENTE = 1;
    const REPORT_GENERALE = 2;
    const FIRST_DATA_ROW_INDEX = 3;

    private $from;
    private $to;
    private $type;

    public function __construct($type, $from , $to)
    {
        $this->type = $type;
        $this->from = $from;
        $this->to   = $to;
    }

    
    public function createReport()
    {
        $conf = App::getInstance()->getConfiguration();
        $templateFile = $conf->getProperty('/report/contatti/template');

        //$xls = PHPExcel_IOFactory::load($templateFile);
        $xls = new PHPExcel();
        $xls->getProperties()
          ->setCreator("RemaxWay")
          ->setLastModifiedBy("RemaxWay")
          ->setTitle("Report contatti")
          ->setSubject("Report contatti")
          ->setDescription("Report contatti")
          ->setKeywords("Report contatti");
        $xls->setActiveSheetIndex(0);

        $additionalCriteria = '';
        $orderBy = 'order by c.data ASC, u.nominativo ASC';
        if ($this->type == self::REPORT_CONSULENTE) {
            $additionalCriteria = "and lower(cl_2.des) = 'generica'";
            $orderBy = 'order by u.nominativo ASC, c.data ASC';
        }

        $query = <<<EOT
        select
            c.*,
            u.nominativo as nome_destinatario,
            cl_1.des as tipo_contatto,
            cl_2.des as tipo_richiesta,
            cl_3.des as motivo_richiesta,
            cl_4.des as tipo_immobile,
            cl_5.des as camere,
            cl_6.des as fonte_pubblicita
        from contatti c
        left join utenti u on u.id = c.id_utente_destinatario
        left join classificazioni cl_1 on cl_1.id = c.id_tipo_contatto
        left join classificazioni cl_2 on cl_2.id = c.id_tipo_richiesta
        left join classificazioni cl_3 on cl_3.id = c.id_motivo_richiesta
        left join classificazioni cl_4 on cl_4.id = c.id_tipo_immobile
        left join classificazioni cl_5 on cl_5.id = c.id_camere
        left join classificazioni cl_6 on cl_6.id = c.id_fonte_pubblicita
        where
            date_trunc('day', c.data) >= '$this->from'
            and date_trunc('day', c.data) <= '$this->to'
            $additionalCriteria
        $orderBy
EOT;

//        var_dump($query); exit();
        $contatto = new DBContatti();
        $this->log("Avvio query...");
        $contatto->query($query);
        $this->log("Query eseguita");
        $row = self::FIRST_DATA_ROW_INDEX;        
        
        if ($this->type == self::REPORT_GENERALE) {
            
            $sheet = $xls->getActiveSheet();
            
            // Impostazione titolo
            $from = date('d/m/Y', strtotime($this->from));
            $to = date('d/m/Y', strtotime($this->to));
            $sheet->setCellValue("A1", "BLA BLA Contatti dal {$from} al {$to}");
        
            $range = array();
            $row = 0;
            while ($contatto->fetch()) {
                $range[$row++] = array(
                    PHPExcel_Shared_Date::PHPToExcel($contatto->data),
                    $contatto->titolo_chiamante,
                    $contatto->nome_chiamante,
                    $contatto->cognome_chiamante,
                    $contatto->telefono_chiamante,
                    $contatto->email_chiamante,
                    $contatto->comune,
                    $contatto->zona,
                    $contatto->tipo_immobile,
                    $contatto->tipo_richiesta,
                    $contatto->motivo_richiesta,                                    
                    ($contatto->prezzo_min > 0) ? $contatto->prezzo_min : "",
                    ($contatto->prezzo_max > 0) ? $contatto->prezzo_max : "",
                    $contatto->note,           
                    ($contatto->superficie_min > 0) ? $contatto->superficie_min : "",
                    ($contatto->superficie_max > 0) ? $contatto->superficie_max : "",
                    $contatto->camere,
                    ($contatto->prezzo > 0) ? $contatto->prezzo : "",
                    $contatto->tipo_contatto,
                    $contatto->id_maximizer,
                    $contatto->fonte_pubblicita,
                    $contatto->nome_destinatario,
                );
            }
            
            $this->log("range di " .count($range) . " righe");
            $sheet->fromArray($range, NULL, "A".self::FIRST_DATA_ROW_INDEX);
            $this->log("contenuto range impostato");
        }
        
        
//        if ($this->type == self::REPORT_CONSULENTE) {
//            $template->getColumnDimension('J')->setVisible(false);
//            $template->getColumnDimension('V')->setVisible(false);
//        }


//        while ($contatto->fetch()) {
//
//            $sheet = $template;
//
//            if ($this->type == self::REPORT_CONSULENTE) {
//                $sheetName = substr(preg_replace('/[^0-9A-Za-z\-_]/', '_', $contatto->nome_destinatario), 0, 30);
//                if ($sheetName == '') {
//                    $sheetName = '(senza destinatario)';
//                }
//                //error_log($sheetName);
//                $sheet = $xls->getSheetByName($sheetName);
//
//                // Clonazione foglio
//                if ($sheet == NULL) {
//                    $row = self::FIRST_DATA_ROW_INDEX;
//                    $sheet = $template->copy();
//                    $sheet->setTitle($sheetName);
//                    $xls->addSheet($sheet, null);
//                }
//            }
//
//            
//
//            // inserimento dettagli
//            $range = array(
//                array(
//                    PHPExcel_Shared_Date::PHPToExcel($contatto->data),
//                    $contatto->titolo_chiamante,
//                    $contatto->nome_chiamante,
//                    $contatto->cognome_chiamante,
//                    $contatto->telefono_chiamante,
//                    $contatto->email_chiamante,
//                    $contatto->comune,
//                    $contatto->zona,
//                    $contatto->tipo_immobile,
//                    $contatto->tipo_richiesta,
//                    $contatto->motivo_richiesta,                                    
//                    ($contatto->prezzo_min > 0) ? $contatto->prezzo_min : "",
//                    ($contatto->prezzo_max > 0) ? $contatto->prezzo_max : "",
//                    $contatto->note,           
//                    ($contatto->superficie_min > 0) ? $contatto->superficie_min : "",
//                    ($contatto->superficie_max > 0) ? $contatto->superficie_max : "",
//                    $contatto->camere,
//                    ($contatto->prezzo > 0) ? $contatto->prezzo : "",
//                    $contatto->tipo_contatto,
//                    $contatto->id_maximizer,
//                    $contatto->fonte_pubblicita,
//                    $contatto->nome_destinatario,
//                )
//            );
//            $sheet->fromArray($range, NULL, "A$row");
//            /*
//            $sheet->setCellValue("A$row", PHPExcel_Shared_Date::PHPToExcel($contatto->data));
//            $sheet->setCellValue("B$row", $contatto->titolo_chiamante);
//            $sheet->setCellValue("C$row", $contatto->nome_chiamante);
//            $sheet->setCellValue("D$row", $contatto->cognome_chiamante);
//            $sheet->setCellValue("E$row", $contatto->telefono_chiamante);
//            $sheet->setCellValue("F$row", $contatto->email_chiamante);
//            $sheet->setCellValue("G$row", $contatto->comune);
//            $sheet->setCellValue("H$row", $contatto->zona);
//            $sheet->setCellValue("I$row", $contatto->tipo_immobile);
//            $sheet->setCellValue("J$row", $contatto->tipo_richiesta);
//            $sheet->setCellValue("K$row", $contatto->motivo_richiesta);                                    
//            if ($contatto->prezzo_min > 0) $sheet->setCellValue("L$row", $contatto->prezzo_min);
//            if ($contatto->prezzo_max > 0) $sheet->setCellValue("M$row", $contatto->prezzo_max);
//            $sheet->setCellValue("N$row", $contatto->note);            
//            if ($contatto->superficie_min > 0) $sheet->setCellValue("O$row", $contatto->superficie_min);
//            if ($contatto->superficie_max > 0) $sheet->setCellValue("P$row", $contatto->superficie_max);
//            $sheet->setCellValue("Q$row", $contatto->camere);
//            if ($contatto->prezzo > 0) $sheet->setCellValue("R$row", $contatto->prezzo);            
//            $sheet->setCellValue("S$row", $contatto->tipo_contatto);
//            $sheet->setCellValue("T$row", $contatto->id_maximizer);
//            $sheet->setCellValue("U$row", $contatto->fonte_pubblicita);
//            $sheet->setCellValue("V$row", $contatto->nome_destinatario);
//             */
//            $row++;
//        }
//
//        if ($this->type == self::REPORT_CONSULENTE) {
//            $xls->removeSheetByIndex(0);
//        }

        $conf = App::getInstance()->getConfiguration();
        $basePath = $conf->getProperty("/temp-dir") . DIRECTORY_SEPARATOR;

        $fileName = $basePath . uniqid("report_contatti_") . ".xlsx";       
        
        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->setPreCalculateFormulas(false);
        $this->log("avvio salvataggio...");
        $objWriter->save($fileName);
        $this->log("salvato!");

        $xls->disconnectWorksheets();
        unset($xls);

        return $fileName;
    }
    
    
    private function log($msg)
    {
        error_log($msg);
    }
}
