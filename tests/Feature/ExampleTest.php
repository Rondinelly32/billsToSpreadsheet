<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use mikehaertl\pdftk\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;
use thiagoalessio\TesseractOCR\TesseractOCR;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $string = json_encode((new TesseractOCR('fatura-print.jpeg'))->lang('por')
            ->run());

        $stringArray = explode('\n', $string);

        $tableTitle = $stringArray[0];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $tableTitle);

        $filteredArray = array_values(array_filter($stringArray, function ($value) {
            return str_contains($value, 'R$');
        }));



        $letter = 'B';

        foreach ($filteredArray as $key => $value) {
            if(str_contains($value, 'R$') && !empty($value)) {
                $subarray = explode('R$', $value);
                foreach ($subarray as $subvalue) {
                    $letter = $letter == 'A' ? 'B' : 'A';
                    if(!$letter) {
                        continue;
                    }
                        $sheet->setCellValue($letter . $key+2, $subvalue);
                }
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');

        echo $string;
    }

    public function testImportPdfText() {

        // Example: Remove password from a PDF
        $pdf = new Pdf;
        $result = $pdf->addFile('fatura-locked.pdf', null, '03566')
            ->saveAs('new.pdf');
        if ($result === false) {
            $error = $pdf->getError();
        }

//        $pdf = new Pdf();
//        $pdf->addFile('fatura.pdf', 'C', '03566')->execute();
//        $pdf->saveAs('new.pdf');

        // Initialize and load PDF Parser library
        $parser = new \Smalot\PdfParser\Parser();

// Source PDF file to extract text
        $file = 'fatura-2.pdf';

// Parse pdf file using Parser library
        $pdf = $parser->parseFile($file);



// Extract text from PDF
        $a =  $pdf->getText();
    }
}
