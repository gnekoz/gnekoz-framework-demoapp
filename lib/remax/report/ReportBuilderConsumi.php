<?php

namespace demo\report;

use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use demo\App;

/**
 * Description of ReportBuilderConsumi
 *
 * @author gneko
 */
class ReportBuilderConsumi
{
    const FLG_ADDEBITATO_TUTTI = 0;
    const FLG_ADDEBITATO_NO = 1;
    const FLG_ADDEBITATO_SI = 2;
    
    const months = array(
        1 => 'gennaio',
        2 => 'febbraio',
        3 => 'marzo',
        4 => 'aprile',
        5 => 'maggio',
        6 => 'giugno',
        7 => 'luglio',
        8 => 'agosto',
        9 => 'settembre',
        10 => 'ottobre',
        11 => 'novembre',
        12 => 'dicembre',
    );
    
    private $consumi;
    private $flgAddebitati;
    private $user;
    
    public function __construct($consumi, $flgAddebitati, $user = false)
    {
        $this->consumi = $consumi;
        $this->flgAddebitati = $flgAddebitati;
        $this->user = $user;
        
        //var_dump($consumi); exit();
    }

    public function createReport()
    {
        $conf = App::getInstance()->getConfiguration();
        $templateFile = $conf->getProperty('/report/consumi/template');

        $xls = PHPExcel_IOFactory::load($templateFile);


        foreach ($this->consumi as $user => $dati)
        {
            $sheet = null;
            
            // Clonazione foglio per ogni consulente
            if (!$this->isSingleUser()) {
                $sheet = clone $xls->getSheetByName("template");
                $sheet->setTitle($user);
                $xls->addSheet($sheet);
            }
            
            // Impostazione intestazione
            $header = "Utente '$user'";
            if ($this->flgAddebitati == self::FLG_ADDEBITATO_TUTTI) {
                $header .= " - tutti i consumi";
            } else if ($this->flgAddebitati == self::FLG_ADDEBITATO_NO) {
                $header .= " - consumi da addebitare";
            } else if ($this->flgAddebitati == self::FLG_ADDEBITATO_SI) {
                $header .= " - consumi addebitati";
            }           
            
            if (!$this->isSingleUser()) {
                $sheet->setCellValue("A1", $header);
            }
            
            // inserimento dettagli consumi
            $row = 3;
            foreach ($dati as $consumo)
            {
                if ($this->isSingleUser()) {
                    $mese = date("n", strtotime($consumo->data));
                    $titoloScheda = self::months[$mese];
                    if ($xls->sheetNameExists($titoloScheda)) {
                        $sheet = $xls->getSheetByName($titoloScheda);
                    } else {
                        
                        // Creazione totale su eventuale foglio precedente
                        if ($sheet != null) {
                            $lastRow = $sheet->getHighestDataRow();
                            $this->createSummaryBlock($sheet, $lastRow + 1);
                        }
                        
                        $sheet = clone $xls->getSheetByName('template');
                        $sheet->setTitle($titoloScheda);
                        $sheet->setCellValue("A1", $header);
                        $xls->addSheet($sheet);
                        $row = 3;
                    }
                }
                
                $stato = $consumo->flg_addebitato == 1 ? "addebitato" : "da addebitare";
                
                $sheet->setCellValue("A$row", PHPExcel_Shared_Date::PHPToExcel($consumo->data));
                $sheet->setCellValue("B$row", $consumo->des_prodotto);
                $sheet->setCellValue("C$row", $stato);
                $sheet->setCellValue("D$row", $consumo->quantita);
                $sheet->setCellValue("E$row", $consumo->prezzo_unitario);
                $sheet->setCellValue("F$row", $consumo->importo);
                $row++; 
            }
            
            // Totale
            $this->createSummaryBlock($sheet, $row);
            
        }
        
        // Rimozione scheda template
        $xls->removeSheetByIndex(0);
        
        $conf = App::getInstance()->getConfiguration();
        $basePath = $conf->getProperty("/temp-dir") . DIRECTORY_SEPARATOR;

        $fileName = $basePath . uniqid("report_consumi_") . ".xlsx";

        $objWriter = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->setIncludeCharts(true);
        $objWriter->setPreCalculateFormulas(true);
        //$objWriter->setUseDiskCaching(true);
        $objWriter->save($fileName);

        $xls->disconnectWorksheets();
        unset($xls);

        return $fileName;
    }
    
    
    private function isSingleUser()
    {
        return $this->user != null;
    }
    
    
    /**
     * 
     * @param type $sheet
     * @param type $row
     */
    private function createSummaryBlock($sheet, $row)
    {
        $previous = $row - 1;
        $styleArray = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '000000')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $sheet->getCell("E$row")->getStyle()->applyFromArray($styleArray);            
        $sheet->setCellValue("E$row", "Totale");
        $sheet->setCellValue("F$row", "=SUM(F3:F$previous)");
        $sheet->getStyle("A$row:F$row")->applyFromArray(array(
            'borders' => array(
                'top' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        ));
    }
}

?>
