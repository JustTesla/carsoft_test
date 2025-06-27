<?php

namespace App\Controller\Admin\Ecu;

use App\Repository\EcuRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/{id}/edit', name: 'admin_ecu_edit', requirements: ['id' => Requirement::UUID_V4])]
class EditEcuAction extends AbstractController
{
    public function __invoke(string $id, Request $request, EntityManagerInterface $entityManager, EcuRepository $repository): Response
    {
        if (null === $ecu = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder($ecu)
            ->setAction($this->generateUrl('admin_ecu_edit', ['id' => $id]))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/edit.html.twig', [
                    'form' => $form,
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_details', ['id' => $id]);
        }

        return $this->render('admin/ecu/edit.html.twig', [
            'form' => $form,
        ]);
    }
}