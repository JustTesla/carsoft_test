<?php

namespace App\Controller\Admin\Ecu\Software;

use App\Repository\EcuSoftwareRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/software/{id}/delete', name: 'admin_ecu_software_delete', requirements: ['id' => Requirement::UUID_V4])]
class DeleteSoftwareAction extends AbstractController
{
    public function __invoke(string $id, EntityManagerInterface $entityManager, EcuSoftwareRepository $repository): Response
    {
        if (null === $ecuSoftware = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($ecuSoftware);
        $entityManager->flush();

        return $this->redirectToRoute('admin_ecu_details', ['id' => $ecuSoftware->getEcu()->getId()]);
    }
}