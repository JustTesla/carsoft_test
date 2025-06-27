<?php

namespace App\Controller\Admin\Ecu\Software\Service;

use App\Repository\EcuSoftwareServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/software/service/{ecuSoftwareServiceId}/delete', name: 'admin_ecu_software_service_delete', requirements: ['ecuSoftwareServiceId' => Requirement::UUID_V4])]
class DeleteServiceAction extends AbstractController
{
    public function __invoke(string $ecuSoftwareServiceId, EntityManagerInterface $entityManager, EcuSoftwareServiceRepository $repository): Response
    {
        if (null === $softwareService = $repository->find($ecuSoftwareServiceId)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($softwareService);
        $entityManager->flush();

        return $this->redirectToRoute('admin_ecu_software_details', ['id' => $softwareService->getEcuSoftware()->getId()]);
    }
}