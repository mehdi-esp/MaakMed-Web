<?php

namespace App\Controller;

use App\Entity\Pharmacy;
use App\Repository\InventoryEntryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

#[Route('/inventory')]
class InventoryController extends AbstractController
{    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
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