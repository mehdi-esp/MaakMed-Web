<?php

namespace App\Twig\Components\Inventory;

use App\Entity\Pharmacy;
use App\Form\InventoryType;
use App\Repository\InventoryEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
/** @method Pharmacy getUser() */
#[AsLiveComponent]
class Listing extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {

    }

    #[LiveProp]
    public Pharmacy $initialFormData;
    #[LiveProp]
    public string $fileLink = '';

    #[LiveProp(writable: true)]
    public bool $editing = true;

    public function mount(): void
    {
        $this->initialFormData = $this->getUser();
    }

    #[LiveAction]
    public function save(): void
    {
        $this->formValues['submitted'] = true;
        $this->submitForm();

        if (!$this->form->isValid()) {
            return;
        }
        $this->editing = true;
        $this->entityManager->flush();
        $this->addFlash('success', 'Save was successful!');

    }


    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(
            InventoryType::class,
            $this->initialFormData
        );
    }

    #[LiveAction]
    public function export(InventoryEntryRepository $inventoryRepository): StreamedResponse
    {
        $temp_file = $this->getSheet($inventoryRepository);

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
    #[LiveAction]
    public function generateAndUploadFile(InventoryEntryRepository $inventoryRepository): RedirectResponse
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
        $temp_file .= '.xlsx'; // Add the .xlsx extension to the temporary file
        $writer->save($temp_file);

        // Initialize Guzzle client
        $client = new Client();

        // Prepare the multipart body
        $multipart = [
            [
                'name'     => 'file',
                'contents' => fopen($temp_file, 'r')
            ]
        ];

        // Upload the file to file.io using Guzzle
        try {
            $response = $client->request('POST', 'https://file.io/?expires=1d', [
                'headers' => [
                    'File.io-API-Key' => '***REMOVED***',
                ],
                'multipart' => $multipart,
            ]);

            // Decode the response
            $data = json_decode($response->getBody()->getContents(), true);

            // Return the file.io link
            $this->fileLink = $data['link'];
        } catch (GuzzleException $e) {
            // Handle exception
            $this->fileLink = 'Error: ' . $e->getMessage();
        }

        // Redirect the user to the generated link
        return new RedirectResponse($this->fileLink);
    }

    public function getSheet(InventoryEntryRepository $inventoryRepository): string|false
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