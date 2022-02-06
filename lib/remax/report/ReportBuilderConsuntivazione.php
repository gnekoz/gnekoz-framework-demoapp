<?php

namespace demo\report;

use demo\App;

/**
 * @author gneko
 *
 */
class ReportBuilderConsuntivazione
{
    /**
     * Nome del foglio template
     */
    const TEMPLATE_SHEET_NAME = 'template';

    const SUMMARY_SHEET_NAME = 'Totali';

    const GENERALE_FIRST_ROW = 6;
    
    const CHART_CELL_HEIGHT = 15;
    
    const CHART_FIRSTLINE_TOP_CELL = 17;
    
    const CHART_SECONDLINE_TOP_CELL = 33;
    
    
    /**
     * Prima riga originale (prima dell'inserimento delle righe per i 
     * consulenti) dei parametri minimi mensili
     */
    const GENERALE_FIRST_PARAM_ORIGINAL_ROW = 22;
    
    const GENERALE_PARAMS_COUNT = 5;
    
    const GENERALE_PARAMS_COLUMN = 'D';
    

    /**
     * Tipo di riepilogo consuntivazione per consulente
     */
    const CONSUNTIVAZIONE_CONSULENTE = 1;

    /**
     * Tipo di riepilogo consuntivazione generale
     */
    const CONSUNTIVAZIONE_GENERALE = 2;


    private static $monthList = array('Gennaio', 'Febbraio', 'Marzo',
        'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto',
        'Settembre', 'Ottobre', 'Novembre', 'Dicembre');

    private $consuntivazioni = array();
    private $budget = 0;
    private $criteriaDes = "";
    private $reportType = self::CONSUNTIVAZIONE_CONSULENTE;

    /**
     *
     * @param type $consuntivazioni
     * @param type $budget
     * @param type $criteriaDes
     * @param type $reportType
     */
    public function __construct($consuntivazioni,
                                $budget,
                                $criteriaDes,
                                $reportType)
    {
        $this->consuntivazioni = $consuntivazioni;
        $this->budget = $budget;
        $this->criteriaDes = $criteriaDes;
        $this->reportType = $reportType;
    }


    /**
     *
     */
    private function initCaching()
    {
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        $cacheSettings = array('memoryCacheSize' => '1MB');
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
    }


    /**
     *
     */
    public function createReport()
    {
        //$this->initCaching();
        switch ($this->reportType) {
            case self::CONSUNTIVAZIONE_CONSULENTE:
                return $this->createReportConsulente();
            case self::CONSUNTIVAZIONE_GENERALE:
                return $this->createReportGenerale();
        }
    }


    /**
     *
     * @return string
     */
    public function createReportConsulente()
    {
        // Ottengo il nome del file del report
        $conf = App::getInstance()->getConfiguration();
        $templateFile = $conf->getProperty('/report/consuntivazione_consulente/template');
        
        // Ottengo il path di base dei file di output
        $basePath = $conf->getProperty("/temp-dir") . DIRECTORY_SEPARATOR;

        // Lettura del file del report
        $reader = new \PHPExcel_Reader_Excel2007();
        //$reader->setReadEmptyCells(false);
        $reader->setIncludeCharts(false);
        $xls = $reader->load($templateFile);


        // Lettura foglio template
        $template = $xls->getSheetByName("template");
               
        $recyleTemplate = false;
        
        // Ciclo sulle consuntivazioni raggruppate per utente
        $uc = 0;
        foreach ($this->consuntivazioni as $user) {
            
            // Incremento contatore consulenti elaborati
            $uc++;
            
            $userName = $user['nominativo'];
            $sheetName = str_replace(' ', '_', $userName);
            
            /*
             * Riutilizzo il foglio "template" per l'ultimo consulente.
             * Se non lo faccio e clonassi tutti i fogli per poi eliminare il
             * foglio template (primo approccio) ottengo un crash di Excel
             * in fase di stampa e in anteprima di stampa
             */ 
            $recyleTemplate = $uc == count($this->consuntivazioni);
            if ($recyleTemplate) {
                //error_log("Riciclo il template");
                $sheet = $template;
            } else {
                //error_log("Clono il template");
                $sheet = $template->copy();
            }          
            $sheet->setTitle($sheetName);

            //error_log("Elaborazione utente $userName\nMemoria in uso: ".memory_get_usage(true));
            
            $sheet->setCellValue("A1", $userName);

            // Ciclo sui mesi della consuntivazione
            foreach ($user['consuntivazioni'] as $month => $data) {

                /*
                 * Inserisco i valori alla riga con numero pari a 3 + mese.
                 * Es. i dati di gennaio cominciano alla posizione B4, ovvero
                 * colonna B e riga 3 + 1 (1 = gennaio)
                 */
                $row = 3 + $month;
                $sheet->setCellValue("B{$row}", $data->gen_nuo_con);
                $sheet->setCellValue("C{$row}", $data->gen_not);
                $sheet->setCellValue("D{$row}", $data->gen_ric_spe);
                $sheet->setCellValue("E{$row}", $data->gen_inc);
                $sheet->setCellValue("F{$row}", $data->app_ven);
                $sheet->setCellValue("G{$row}", $data->app_aff);
                $sheet->setCellValue("H{$row}", $data->app_acq);
                $sheet->setCellValue("I{$row}", $data->pro_acq);
                $sheet->setCellValue("J{$row}", $data->pro_acq_col);
                $sheet->setCellValue("K{$row}", $data->pro_loc);
                $sheet->setCellValue("L{$row}", $data->pro_loc_col);
                $sheet->setCellValue("M{$row}", $data->tra_ven);
                $sheet->setCellValue("N{$row}", $data->tra_aff);

                // Devo ricreare i grafici manualmente
                $this->addChart1($xls, $sheet);
                $this->addChart2($xls, $sheet);
                $this->addChart3($xls, $sheet);
                $this->addChart4($xls, $sheet);
                $this->addChart5($xls, $sheet);
            }


            // Aggiunta del foglio
            if (!$recyleTemplate) {
                //error_log("Aggiungo il foglio");
                $xls->addSheet($sheet, null);
            }
        }


        $fileName = $basePath . uniqid("report_consuntivazione_") . ".xlsx";

        //echo memory_get_usage(true); exit();
        $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->setIncludeCharts(true);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($fileName);

        $xls->disconnectWorksheets();
        unset($xls);

        return $fileName;
    }

    
    /**
     * 
     * @param \PHPExcel $ea
     * @param \PHPExcel_Worksheet $ews
     */
    private function addChart1(\PHPExcel $ea, \PHPExcel_Worksheet $ews) 
    {
        //The below line should be moved into addAnalysis but we move this here to show that $ews is actually referring to the sheet
        $sheet = $ews->getTitle();
        $chartTitle = new \PHPExcel_Chart_Title("NUOVI CONTATTI");
                
        
        // Set the data serie labels 
        $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!B1", NULL, 1),);
        
        // Set X-Axis Labels
        $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', "$sheet!A4:A15", NULL, 2),);
        
        // Set data serie values
        $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!B4:B15", NULL, 2),);
        
        // Build a dataserie
        $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, null, range(0, count($dsv) - 1), $dsl, $xal, $dsv
        );
        
        // A layout for the Chart
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        //$layout->setShowPercent(true);
        
        // Set series in the plot area
        $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
        
        // Set legend
        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //Create Chart
        $chart = new \PHPExcel_Chart(
                'chart1', $chartTitle, $legend, $pa, true, 0, NULL, NULL
        );

        $chart->setTopLeftPosition('A' . self::CHART_FIRSTLINE_TOP_CELL);
        $chart->setBottomRightPosition('H' . (self::CHART_FIRSTLINE_TOP_CELL + self::CHART_CELL_HEIGHT));
        $ews->addChart($chart);
    }
    
    
    /**
     * 
     * @param \PHPExcel $ea
     * @param \PHPExcel_Worksheet $ews
     */
    private function addChart2(\PHPExcel $ea, \PHPExcel_Worksheet $ews) 
    {
        //The below line should be moved into addAnalysis but we move this here to show that $ews is actually referring to the sheet
        $sheet = $ews->getTitle();
        $chartTitle = new \PHPExcel_Chart_Title("RICHIESTE SPECIFICHE");
                
        
        // Set the data serie labels 
        $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!D1", NULL, 1),);
        
        // Set X-Axis Labels
        $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', "$sheet!A4:A15", NULL, 2),);
        
        // Set data serie values
        $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!D4:D15", NULL, 2),);
        
        // Build a dataserie
        $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, null, range(0, count($dsv) - 1), $dsl, $xal, $dsv
        );
        
        // A layout for the Chart
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        //$layout->setShowPercent(true);
        
        // Set series in the plot area
        $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
        
        // Set legend
        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //Create Chart
        $chart = new \PHPExcel_Chart(
                'chart1', $chartTitle, $legend, $pa, true, 0, NULL, NULL
        );

        $chart->setTopLeftPosition('H' . self::CHART_FIRSTLINE_TOP_CELL);
        $chart->setBottomRightPosition('P' . (self::CHART_FIRSTLINE_TOP_CELL + self::CHART_CELL_HEIGHT));
        $ews->addChart($chart);
    }    
    

    /**
     * 
     * @param \PHPExcel $ea
     * @param \PHPExcel_Worksheet $ews
     */
    private function addChart3(\PHPExcel $ea, \PHPExcel_Worksheet $ews) 
    {
        //The below line should be moved into addAnalysis but we move this here to show that $ews is actually referring to the sheet
        $sheet = $ews->getTitle();
        $chartTitle = new \PHPExcel_Chart_Title("INCARICHI");
                
        
        // Set the data serie labels 
        $dsl = array(new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!E1", NULL, 1),);
        
        // Set X-Axis Labels
        $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', "$sheet!A4:A15", NULL, 2),);
        
        // Set data serie values
        $dsv = array(new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!E4:E15", NULL, 2),);
        
        // Build a dataserie
        $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, null, range(0, count($dsv) - 1), $dsl, $xal, $dsv
        );
        
        // A layout for the Chart
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        //$layout->setShowPercent(true);
        
        // Set series in the plot area
        $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
        
        // Set legend
        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //Create Chart
        $chart = new \PHPExcel_Chart(
                'chart1', $chartTitle, $legend, $pa, true, 0, NULL, NULL
        );

        $chart->setTopLeftPosition('P' . self::CHART_FIRSTLINE_TOP_CELL);
        $chart->setBottomRightPosition('X' . (self::CHART_FIRSTLINE_TOP_CELL + self::CHART_CELL_HEIGHT));
        $ews->addChart($chart);
    }    
    
  
    /**
     * 
     * @param \PHPExcel $ea
     * @param \PHPExcel_Worksheet $ews
     */
    private function addChart4(\PHPExcel $ea, \PHPExcel_Worksheet $ews) 
    {
        //The below line should be moved into addAnalysis but we move this here to show that $ews is actually referring to the sheet
        $sheet = $ews->getTitle();
        $chartTitle = new \PHPExcel_Chart_Title("APPUNTAMENTI");
                
        
        // Set the data serie labels 
        $dsl = array(
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!F2", NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!G2", NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!H2", NULL, 1),
        );
        
        // Set X-Axis Labels
        $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', "$sheet!A4:A15", NULL, 2),);
        
        // Set data serie values
        $dsv = array(
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!F4:F15", NULL, 2),
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!G4:G15", NULL, 2),
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!H4:H15", NULL, 2),
        );
        
        // Build a dataserie
        $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, null, range(0, count($dsv) - 1), $dsl, $xal, $dsv
        );
        
        // A layout for the Chart
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        //$layout->setShowPercent(true);
        
        // Set series in the plot area
        $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
        
        // Set legend
        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //Create Chart
        $chart = new \PHPExcel_Chart(
                'chart1', $chartTitle, $legend, $pa, true, 0, NULL, NULL
        );

        $chart->setTopLeftPosition('A' . self::CHART_SECONDLINE_TOP_CELL);
        $chart->setBottomRightPosition('K' . (self::CHART_SECONDLINE_TOP_CELL+ self::CHART_CELL_HEIGHT));
        $ews->addChart($chart);
    }    
    
    
    /**
     * 
     * @param \PHPExcel $ea
     * @param \PHPExcel_Worksheet $ews
     */
    private function addChart5(\PHPExcel $ea, \PHPExcel_Worksheet $ews) 
    {
        //The below line should be moved into addAnalysis but we move this here to show that $ews is actually referring to the sheet
        $sheet = $ews->getTitle();
        $chartTitle = new \PHPExcel_Chart_Title("PROPOSTE");
                
        
        // Set the data serie labels 
        $dsl = array(
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!I2", NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!J2", NULL, 1),
            new \PHPExcel_Chart_DataSeriesValues('String',  "$sheet!K2", NULL, 1),
        );
        
        // Set X-Axis Labels
        $xal = array(new \PHPExcel_Chart_DataSeriesValues('String', "$sheet!A4:A15", NULL, 2),);
        
        // Set data serie values
        $dsv = array(
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!I4:I15", NULL, 2),
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!J4:J15", NULL, 2),
            new \PHPExcel_Chart_DataSeriesValues('Number', "$sheet!K4:K15", NULL, 2),
        );
        
        // Build a dataserie
        $ds = new \PHPExcel_Chart_DataSeries(
                \PHPExcel_Chart_DataSeries::TYPE_BARCHART, null, range(0, count($dsv) - 1), $dsl, $xal, $dsv
        );
        
        // A layout for the Chart
        $layout = new \PHPExcel_Chart_Layout();
        $layout->setShowVal(true);
        //$layout->setShowPercent(true);
        
        // Set series in the plot area
        $pa = new \PHPExcel_Chart_PlotArea($layout, array($ds));
        
        // Set legend
        $legend = new \PHPExcel_Chart_Legend(\PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

        //Create Chart
        $chart = new \PHPExcel_Chart(
                'chart1', $chartTitle, $legend, $pa, true, 0, NULL, NULL
        );

        $chart->setTopLeftPosition('K' . self::CHART_SECONDLINE_TOP_CELL);
        $chart->setBottomRightPosition('X' . (self::CHART_SECONDLINE_TOP_CELL+ self::CHART_CELL_HEIGHT));
        $ews->addChart($chart);
    }        
    
    
    /**
     *
     * @return string
     */
    public function createReportGenerale()
    {
        // Ottengo il nome del file del report
        $conf = App::getInstance()->getConfiguration();
        $templateFile = $conf->getProperty('/report/consuntivazione_generale/template');

        // Lettura del file del report
        $reader = new \PHPExcel_Reader_Excel2007();
        $reader->setIncludeCharts(false);
        $xls = $reader->load($templateFile);        

        // Ciclo sui mesi
        foreach (self::$monthList as $monthId => $monthName) {

            // Per ogni mese recupero il corrispondente foglio
            $sheet = $xls->getSheetByName($monthName);
            
            /*
             * Inserisco n righe nuove
             * Le n righe sono inserite tra le 2 righe fittizie (con utente --)
             * in modo da ereditarne la formattazione e preservare le formule
             * dei totali.
             * Immediatamente dopo l'inserimento nelle n righe vengono eliminate
             * le righe fittizie
             */
            $sheet->insertNewRowBefore(self::GENERALE_FIRST_ROW + 1,
                                       count($this->consuntivazioni));

            // Elimino le righe vuote prima e dopo l'inserimento
            $sheet->removeRow(count($this->consuntivazioni) + self::GENERALE_FIRST_ROW + 1);
            $sheet->removeRow(self::GENERALE_FIRST_ROW);

            $row = self::GENERALE_FIRST_ROW;

            $rangeData = array();
            foreach ($this->consuntivazioni as $user) {

                /*
                 * Popolamento riga con valori di default
                 *
                 * ATTENZIONE ALLE FORMULE!!!! Ci ho perso ore: nell'IF il
                 * separatore dei blocchi e' la virgola, non il punto e virgola
                 * che si vedono in interfaccia di Excel!!!
                 */
                $rowData = array(
                    $user['nominativo'],
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    '0',
                    "=IF(H{$row}=0,0,E{$row}/H{$row})",
                    "=IF(H{$row}=0,0,E{$row}/H{$row})",
                    "=IF(F{$row}=0,0,(J{$row}+I{$row})/F{$row})",
                    "=IF(F{$row}=0,0,(J{$row}+I{$row})/F{$row})",
                    "=IF(B{$row}=0,0,E{$row}/B{$row})",
                    "=IF(B{$row}=0,0,E{$row}/B{$row})",
                    "=IF(B{$row}=0,0,(J{$row}+I{$row})/B{$row})",
                    "=IF(B{$row}=0,0,(J{$row}+I{$row})/B{$row})",
                    "=IF(D{$row}=0,0,(I{$row}+J{$row})/D{$row})",
                    "=IF(D{$row}=0,0,(I{$row}+J{$row})/D{$row})",
                );


                /*
                 * Se per il mese e l'utente corrente ci sono dei dati aggiorno
                 * la riga calcolata fin qui
                 */
                if (isset($user['consuntivazioni'][$monthId + 1])) {
                    $data = $user['consuntivazioni'][$monthId + 1];
                    $rowData[1] = $data->gen_nuo_con;
                    $rowData[2] = $data->gen_not;
                    $rowData[3] = $data->gen_ric_spe;
                    $rowData[4] = $data->gen_inc;
                    $rowData[5] = $data->app_ven;
                    $rowData[6] = $data->app_aff;
                    $rowData[7] = $data->app_acq;
                    $rowData[8] = $data->pro_acq;
                    $rowData[9] = $data->pro_acq_col;
                    $rowData[10] = $data->pro_loc;
                    $rowData[11] = $data->pro_loc_col;
                    $rowData[12] = $data->tra_ven;
                    $rowData[13] = $data->tra_aff;
                }
                $rangeData[] = $rowData;
                $row++;
            }
            $sheet->fromArray($rangeData, null, 'A'.self::GENERALE_FIRST_ROW);
            $sheet->garbageCollect();
            
            // Misc
            $rowCount = count($this->consuntivazioni);
            
            // Sistemazione riferimenti al primo foglio nei parametri minimi
            $this->fixParametersReferences($sheet, $rowCount);

            
            /*
             * Propagazione formattazione condizionale
             * 
             * La formattazione condizionale presente nel foglio template
             * rimane vincolata al range indicato nel template (ovvero, è valida
             * solo per il range di celle delle 2 righe fittizie).
             * Bisogna modificare il range di applicazione della formattazione
             * condizionale estendendolo a tutte le celle delle righe inserite
             */            
            $this->fixAndPropagateConditionalFormatting($sheet, "B", $rowCount);
            $this->fixAndPropagateConditionalFormatting($sheet, "E", $rowCount);
            $this->fixAndPropagateConditionalFormatting($sheet, "H", $rowCount);
            $this->fixAndPropagateConditionalFormatting($sheet, "I", $rowCount);
            $this->fixAndPropagateConditionalFormatting($sheet, "D", $rowCount);
            $this->fixAndPropagateConditionalFormatting($sheet, "J", $rowCount);
        }


        // Foglio totali
        $sheet = $xls->getSheetByName(self::SUMMARY_SHEET_NAME);

        // Inserisco n righe nuove
        $sheet->insertNewRowBefore(self::GENERALE_FIRST_ROW + 1,
                                   count($this->consuntivazioni));

        // Elimino le righe vuote prima e dopo l'inserimento
        $sheet->removeRow(count($this->consuntivazioni) + self::GENERALE_FIRST_ROW + 1);
        $sheet->removeRow(self::GENERALE_FIRST_ROW);

        $row = self::GENERALE_FIRST_ROW;

        $rangeData = array();
        foreach ($this->consuntivazioni as $user) {
            $rangeData[] = array(
                $user['nominativo'],
                $this->getSummaryFormula("B{$row}"),
                $this->getSummaryFormula("C{$row}"),
                $this->getSummaryFormula("D{$row}"),
                $this->getSummaryFormula("E{$row}"),
                $this->getSummaryFormula("F{$row}"),
                $this->getSummaryFormula("G{$row}"),
                $this->getSummaryFormula("H{$row}"),
                $this->getSummaryFormula("I{$row}"),
                $this->getSummaryFormula("J{$row}"),
                $this->getSummaryFormula("K{$row}"),
                $this->getSummaryFormula("L{$row}"),
                $this->getSummaryFormula("M{$row}"),
                $this->getSummaryFormula("N{$row}"),
                "=IF(H{$row}=0,0,E{$row}/H{$row})",
                "=IF(H{$row}=0,0,E{$row}/H{$row})",
                "=IF(F{$row}=0,0,(J{$row}+I{$row})/F{$row})",
                "=IF(F{$row}=0,0,(J{$row}+I{$row})/F{$row})",
                "=IF(B{$row}=0,0,E{$row}/B{$row})",
                "=IF(B{$row}=0,0,E{$row}/B{$row})",
                "=IF(B{$row}=0,0,(J{$row}+I{$row})/B{$row})",
                "=IF(B{$row}=0,0,(J{$row}+I{$row})/B{$row})",
                "=IF(D{$row}=0,0,(I{$row}+J{$row})/D{$row})",
                "=IF(D{$row}=0,0,(I{$row}+J{$row})/D{$row})",
            );
            $row++;
        }
        $sheet->fromArray($rangeData, null, 'A'.self::GENERALE_FIRST_ROW);
        
        // Misc
        $rowCount = count($this->consuntivazioni);
        
        // Sistemazione riferimenti al primo foglio nei parametri minimi
        $this->fixParametersReferences($sheet, $rowCount);
        
        // Propagazione formattazione condizionale
        $this->fixAndPropagateConditionalFormatting($sheet, "B", $rowCount);
        $this->fixAndPropagateConditionalFormatting($sheet, "E", $rowCount);
        $this->fixAndPropagateConditionalFormatting($sheet, "H", $rowCount);
        $this->fixAndPropagateConditionalFormatting($sheet, "I", $rowCount);
        $this->fixAndPropagateConditionalFormatting($sheet, "D", $rowCount);
        $this->fixAndPropagateConditionalFormatting($sheet, "J", $rowCount);        

        $basePath = $conf->getProperty("/temp-dir") . DIRECTORY_SEPARATOR;
        $fileName = $basePath . uniqid("report_consuntivazione_") . ".xlsx";

        $objWriter = \PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
        $objWriter->setIncludeCharts(false);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save($fileName);

        $xls->disconnectWorksheets();
        unset($xls);

        return $fileName;
    }
    
    
    /**
     * Propaga la formattazione condizionale delle prime 2 righe della colonna
     * su tutte le righe indicare
     * 
     * Prima della propagazione è necessario correggere i riferimenti alle celle
     * nella formattazione condizionale: 
     * l'inserimento delle righe per i consulenti sballa il riferimento alla
     * cella con i parametri minimi che viene utilizzata nella formattazione
     * condizionale. Vengono quindi modificati i riferimenti aggiungendo 
     * un offset
     * 
     * 
     * @param type $sheet - foglio su cui operare
     * @param type $col - colonna in esame
     * @param type $rowCount - numero di righe da elaborare
     */
    private function fixAndPropagateConditionalFormatting($sheet, $col, $rowCount)
    {
        $fr = self::GENERALE_FIRST_ROW; // First row
        $sr = $fr + 1; // Second row
        $lr = $fr + $rowCount - 1; // Last Row            
        $cs = $sheet->getConditionalStyles("{$col}{$fr}:{$col}{$sr}");
        
        // Applico offset ai riferimenti di cella (es. $D$25 -> $D$26)
        $conds = $cs[0]->getConditions();
        $pattern = '/^(\$[A-z]+\$)(\d+)$/';
        for ($i = 0; $i < count($conds); $i++) {
            $cond = $conds[$i];            
            if (preg_match($pattern, $cond, $matches)) {              
                $cond = $matches[1].(((int) $matches[2]) + $rowCount - 2);
            }
            //var_dump($cond); exit();
            $conds[$i] = $cond;            
        }
        $cs[0]->setConditions($conds);
        $sheet->getStyle("{$col}{$fr}:{$col}{$lr}")->setConditionalStyles($cs);        
    }

    
    /**
     * Siccome l'aggiunta di righe per i consulenti sballa i riferimenti ai 
     * parametri minimi mensili per i mesi > gennaio è necessario applicare
     * un offset ai riferimenti delle celle
     * 
     * @param type $sheet
     */
    private function fixParametersReferences($sheet, $rowCount)
    {
        $pattern = '/^(=[A-z0-9_-]+!\$?[A-z]+\$?)(\d+)(.*)$/';
        $offset = $rowCount - 2;
        $fr = self::GENERALE_FIRST_PARAM_ORIGINAL_ROW + $offset;
        $lr = $fr + self::GENERALE_PARAMS_COUNT;
        //$sheet = new \PHPExcel_Worksheet(); // REMOVE
        for ($i = $fr; $i <= $lr; $i++) {
            $cell = $sheet->getCell(self::GENERALE_PARAMS_COLUMN . $i);            
            if (!$cell->isFormula()) {
                continue;
            }
            $val = $cell->getValue();
            if (preg_match($pattern, $val, $matches)) {
                $val = $matches[1] 
                     . (((int) $matches[2]) + $offset) 
                     . $matches[3];
            }
            $cell->setValue($val);            
        }
    }
    
    /**
     * Costruisce la formula da inserire nelle celle del foglio totali e che
     * somma le corrispondenti celle dei fogli dei singoli mesi
     */
    private function getSummaryFormula($cell)
    {
        $formula = array();
        foreach (self::$monthList as $name) {
            $formula[] = "$name!$cell";
        }
        return '='.join('+', $formula);
    }
}
