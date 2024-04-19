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
use App\Service\InventoryService;

/** @method Pharmacy getUser() */
#[AsLiveComponent]
class Listing extends AbstractController
{
    use DefaultActionTrait;
    use LiveCollectionTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly InventoryService $inventoryService
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
    public function generateAndUploadFile(InventoryEntryRepository $inventoryRepository): RedirectResponse
    {
        // Get the temporary file from the InventoryService
        $temp_file = $this->inventoryService->getSheet($inventoryRepository, $this->getUser());

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


}