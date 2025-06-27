<?php

namespace App\Controller\Admin\Ecu\Software;

use App\Repository\EcuSoftwareRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/software/{id}', name: 'admin_ecu_software_details', requirements: ['id' => Requirement::UUID_V4])]
class ViewSoftwareAction extends AbstractController
{
    public function __invoke(string $id, EcuSoftwareRepository $repository): Response
    {
        if (null === $ecuSoftware = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'admin/ecu/software/details.html.twig', 
            [
                'ecuSoftware' => $ecuSoftware,
            ]
        );
    }
}