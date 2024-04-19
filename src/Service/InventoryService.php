<?php

namespace App\Service;

use App\Repository\InventoryEntryRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InventoryService
{
    public function getSheet(InventoryEntryRepository $inventoryRepository, $user): string|false
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Fetch the inventory data
        $inventoryEntries = $inventoryRepository->findBy(['pharmacy' => $user]);

        // Add data to the spreadsheet
        $sheet->setCellValue('A1', 'Medication');
        $sheet->setCellValue('B1', 'Quantity');
        foreach ($inventoryEntries as $index => $entry) {
            $sheet->setCellValue('A' . ($index + 2), $entry->getMedication()->getName());
            $sheet->setCellValue('B' . ($index + 2), $entry->getQuantity());
        }

        // Create a writer object and save the spreadsheet to a temporary file
        $writer = new Xlsx($spreadsheet);
        $temp_file = $this->getTempFile('inventory', 'xlsx');
        $writer->save($temp_file);
        return $temp_file;
    }

    private function getTempFile($prefix, $extension): string
    {
        $temp_dir = sys_get_temp_dir();
        $temp_file = tempnam($temp_dir, $prefix);
        unlink($temp_file);
        return $temp_file . '.' . $extension;
    }
}