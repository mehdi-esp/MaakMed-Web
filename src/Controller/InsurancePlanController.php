<?php

namespace App\Controller;

use App\Entity\InsurancePlan;
use App\Form\InsurancePlanType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InsurancePlanController extends AbstractController
{
    #[Route('/listInsurancePlan', name: 'app_insurancePlan_list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function listIP(EntityManagerInterface $entityManager): Response
    {
        $insurancePlans = $entityManager->getRepository(InsurancePlan::class)->findAll();
        return $this->render('insurancePlan/listInsurancePlan.html.twig', [
            'insurancePlans' => $insurancePlans,
        ]);
    }



    #[Route('/addInsurancePlan', name: 'app_insurancePlan_add', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $req,EntityManagerInterface $entityManager): Response
    {
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
    #[Route('/{id}/IPedit', name: 'app_insurancePlan_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $req, InsurancePlan $ip, EntityManagerInterface $entityManager): Response
    {
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
    #[Route('/{id}/IPdelete', name: 'app_insurancePlan_delete', methods: ['GET','POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $req, InsurancePlan $ip, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ip);
        $entityManager->flush();
        return $this->redirectToRoute('app_insurancePlan_list', [], Response::HTTP_SEE_OTHER);
    }
}