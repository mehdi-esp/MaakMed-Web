<?php

namespace App\Controller;

use App\Entity\Pharmacy;
use App\Repository\InventoryEntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
#[Route('/inventory')]
class InventoryController extends AbstractController
{
    #[Route('/', name: 'app_inventory_index', methods: ['GET'])]
    #[IsGranted('ROLE_PHARMACY')]
    public function index(InventoryEntryRepository $inventoryRepository): Response
    {
        /** @var Pharmacy $user */

//        $user = $this->getUser();

//        $inventoryEntries = $user->getInventoryEntries();
        return $this->render('inventory/index.html.twig', [
//            'inventoryEntries' => $inventoryEntries,
        ]);
    }

    #[Route('/export', name: 'app_inventory_export', methods: ['GET'])]
    public function export(InventoryEntryRepository $inventoryRepository): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Fetch the inventory data
        $inventoryEntries = $inventoryRepository->findBy(['pharmacy' => $this->getUser()]);

        // Add data to the spreadsheet
        $sheet->setCellValue('A1', 'Medication');
        $sheet->setCellValue('B1', 'Quantity');
        foreach ($inventoryEntries as $index => $entry) {
            $sheet->setCellValue('A' . ($index + 2), $entry->getMedication()->getName());
            $sheet->setCellValue('B' . ($index + 2), $entry->getQuantity());
        }

        // Create a writer object and save the spreadsheet to a temporary file
        $writer = new Xlsx($spreadsheet);
        $temp_file = tempnam(sys_get_temp_dir(), 'inventory');
        $writer->save($temp_file);

        // Create a StreamedResponse object and set the necessary headers
        $response = new StreamedResponse(function () use ($temp_file) {
            $fp = fopen($temp_file, 'rb');
            fpassthru($fp);
            fclose($fp);
            unlink($temp_file); // Delete the temporary file
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'inventory.xlsx'
        ));

        return $response;
    }
}