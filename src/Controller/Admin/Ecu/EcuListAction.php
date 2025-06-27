<?php

namespace App\Controller\Admin\Ecu;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/ecu', name: 'admin_ecu')]
class EcuListAction extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('admin/ecu/index.html.twig', ['menuItem' => 'ecu']);
    }
}