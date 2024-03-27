<?php

namespace App\Controller;
use App\Entity\Admin;
use App\Entity\Doctor;
use App\Entity\Patient;
use App\Repository\PrescriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\PrescriptionVoter;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Jungi\FrameworkExtraBundle\Attribute\QueryParam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;



#[Route('/prescription')]
class PrescriptionController extends AbstractController
{
    #[Route('/', name: 'app_prescription_index', methods: ['GET'])]
    #[IsGranted(PrescriptionVoter::LIST_ALL)]
    public function index(
        PrescriptionRepository $prescriptionRepository,
        UserRepository $userRepository,
        #[QueryParam('doctor')] ?string  $doctorUsername = null,
        #[QueryParam('patient')] ?string $patientUsername = null,
    ): Response
    {
        /** @var Doctor|Admin|Patient $user */
        $user = $this->getUser();
    
        $criteria = [];
    
        if ($doctorUsername) {
            $doctor = $userRepository->findOneByUsername($doctorUsername);
            if (!$doctor instanceof Doctor) {
                throw new BadRequestHttpException('Doctor not found');
            }
            $criteria['visit.doctor'] = $doctor;
        }
    
        if ($patientUsername) {
            $patient = $userRepository->findOneByUsername($patientUsername);
            if (!$patient instanceof Patient) {
                throw new BadRequestHttpException('Patient not found');
            }
            $criteria['visit.patient'] = $patient;
        }
    
        $prescriptions = $prescriptionRepository->findBy($criteria);
    
        $filters = array_filter(['doctor' => $doctor ?? null, 'patient' => $patient ?? null]);
        return $this->render('prescription/index.html.twig', [
            'prescriptions' => $prescriptions,
            'filters' => $filters
        ]);
    }
}
