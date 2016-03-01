<?php
/*
*  @author Norbert Pabian <norbert.pabian@gmail.com>
*  @copyright 2016 npsoftware
*/
require_once(_PS_TOOL_DIR_.'phpexcel/PHPExcel.php');

class EventParticipantsExcel {

    private $participants;
    private $objPHPExcel;

    public function __construct($participants, $name, $date) {
        $this->participants = $participants;

        $this->objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()
            ->setCreator("Labsintown")
            ->setLastModifiedBy("Labsintown")
            ->setTitle($name." ".$date)
            ->setSubject($name." ".$date)
            ->setDescription("List of event participants.")
            ->setKeywords("labsintown participants")
            ->setCategory("labsintown");
    }

    function render() {
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Hello')
            ->setCellValue('B2', 'world!')
            ->setCellValue('C1', 'Hello')
            ->setCellValue('D2', 'world!');

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'_'.$date.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }
}

