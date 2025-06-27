<?php

namespace App\Controller\Admin\Ecu;

use App\Repository\EcuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/{id}/delete', name: 'admin_ecu_delete', requirements: ['id' => Requirement::UUID_V4])]
class DeleteEcuAction extends AbstractController
{
    public function __invoke(string $id, EntityManagerInterface $entityManager, EcuRepository $repository): Response
    {
        if (null === $ecu = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($ecu);
        $entityManager->flush();

        return $this->redirectToRoute('admin_ecu');
    }
}