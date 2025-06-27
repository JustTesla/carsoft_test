<?php

namespace App\Controller\Admin\Ecu;

use App\Repository\EcuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/{id}', name: 'admin_ecu_details', requirements: ['id' => Requirement::UUID_V4])]
class ViewEcuAction extends AbstractController
{
    public function __invoke(string $id, EcuRepository $repository): Response
    {
        if (null === $ecu = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'admin/ecu/details.html.twig', 
            [
                'ecu' => $ecu,
            ]
        );
    }
}