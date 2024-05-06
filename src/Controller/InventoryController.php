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
use App\Service\InventoryService;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;


#[Route('/inventory')]
class InventoryController extends AbstractController
{
    private $inventoryService;

    public function __construct( InventoryService $inventoryService, private readonly Breadcrumbs $breadcrumbs)
    {
        $this->inventoryService = $inventoryService;

    }
    #[Route('/', name: 'app_inventory_index', methods: ['GET'])]
    #[IsGranted('ROLE_PHARMACY')]
    public function index(InventoryEntryRepository $inventoryRepository): Response
    {
        $this->breadcrumbs->addRouteItem("Inventory", "app_inventory_index");

        /** @var Pharmacy $user */

//        $user = $this->getUser();

//        $inventoryEntries = $user->getInventoryEntries();
        return $this->render('inventory/index.html.twig', [
//            'inventoryEntries' => $inventoryEntries,
        ]);
    }

    #[Route('/export', name: 'app_inventory_export', methods: ['GET'])]
    #[IsGranted('ROLE_PHARMACY')]
    public function export(InventoryEntryRepository $inventoryRepository): StreamedResponse
    {
        $temp_file = $this->inventoryService->getSheet($inventoryRepository, $this->getUser());

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