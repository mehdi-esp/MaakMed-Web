<?php

namespace App\Controller;

use App\Entity\Pharmacy;
use App\Repository\InventoryEntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}