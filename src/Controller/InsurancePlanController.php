<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Entity\Patient;
use App\Form\InsurancePlanType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

#[Route('/plan')]
class InsurancePlanController extends AbstractController
{
    public function __construct(
                private readonly Breadcrumbs $breadcrumbs,
            ) {
            }

    #[Route('/', name: 'app_insurancePlan_list', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_PATIENT')")]
    public function listIP(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Plans", "app_insurancePlan_list");
        $repository = $entityManager->getRepository(InsurancePlan::class);
        $user = $this->getUser();
         if ($user instanceof Patient) {
             $plans = $entityManager->getRepository(InsurancePlan::class)->findAll();
             return $this->render('insurance_plan/ListPlansPatient.html.twig', [
                     'plans' => $plans,
             ]);
         }
        $searchTerm = $request->query->get('searchTerm');
        $costFilter = $request->query->get('costFilter');
        $ceilingFilter = $request->query->get('ceilingFilter');
        $queryBuilder = $repository->createQueryBuilder('ip');
        if ($searchTerm) {
            $queryBuilder->andWhere('ip.name LIKE :searchTerm')
                ->setParameter('searchTerm', $searchTerm . '%');
        }
        if ($costFilter) {
            if ($costFilter === 'high') {
                $queryBuilder->orderBy('ip.cost', 'DESC');
            } elseif ($costFilter === 'low') {
                $queryBuilder->orderBy('ip.cost', 'ASC');
            }
        }
        if ($ceilingFilter) {
            if ($ceilingFilter === '>') {
                $queryBuilder->andWhere('ip.ceiling >= 1000');
            } elseif ($ceilingFilter === '<') {
                $queryBuilder->andWhere('ip.ceiling < 1000');
            }
        }
        $insurancePlans = $queryBuilder->getQuery()->getResult();
        return $this->render('insurancePlan/listInsurancePlan.html.twig', [
            'insurancePlans' => $insurancePlans,
        ]);
    }

    #[Route('/new', name: 'app_insurancePlan_add', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $req,EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Plans", "app_insurancePlan_list");
        $this->breadcrumbs->addRouteItem("Add", "app_insurancePlan_add");
        $insurancePlan = new InsurancePlan();
        $form = $this->createForm(InsurancePlanType::class, $insurancePlan);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($insurancePlan);
            $entityManager->flush();

            return $this->redirectToRoute('app_insurancePlan_list', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );
        return $this->render('insurancePlan/addInsurancePlan.html.twig', [
            'insurancePlan' => $insurancePlan,
            'form' => $form->createView(),

        ], $response);
    }
    #[Route('/{id}/edit', name: 'app_insurancePlan_edit', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $req, InsurancePlan $ip, EntityManagerInterface $entityManager): Response
    {
        $this->breadcrumbs->addRouteItem("Plans", "app_insurancePlan_list");
        $this->breadcrumbs->addItem("Edit");
        $this->breadcrumbs->addRouteItem($ip->getId(), "app_insurancePlan_edit", ["id" => $ip->getId()]);
        $form = $this->createForm(InsurancePlanType::class, $ip);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_insurancePlan_list', [], Response::HTTP_SEE_OTHER);
        }

        $response = new Response(
            status: $form->isSubmitted() ?
                Response::HTTP_UNPROCESSABLE_ENTITY :
                Response::HTTP_OK,
        );
        return $this->render('insurancePlan/editInsurancePlan.html.twig', [
            'insurancePlan' => $ip,
            'form' => $form->createView(),

        ], $response);
    }

    #[Route('/{id}/delete', name: 'app_insurancePlan_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $req, InsurancePlan $ip, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ip);
        $entityManager->flush();
        return $this->redirectToRoute('app_insurancePlan_list', [], Response::HTTP_SEE_OTHER);
    }

}