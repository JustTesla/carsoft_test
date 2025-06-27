<?php

namespace App\Controller\Admin\Ecu\Software;

use App\Repository\EcuSoftwareRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/software/{id}/edit', name: 'admin_ecu_software_edit', requirements: ['id' => Requirement::UUID_V4])]
class EditSoftwareAction extends AbstractController
{
    public function __invoke(
        string $id,
        Request $request,
        EntityManagerInterface $entityManager,
        EcuSoftwareRepository $repository
    ): Response {
        if (null === $ecuSoftware = $repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder($ecuSoftware)
            ->setAction($this->generateUrl('admin_ecu_software_edit', ['id' => $id]))
            ->setMethod('POST')
            ->add('version', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ecuSoftware->setVersion($form->get('version')->getData());
            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/software/edit.html.twig', [
                    'form' => $form,
                    'ecuSoftware' => $ecuSoftware,
                    'menuItem' => 'ecu',
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_details', ['id' => $ecuSoftware->getEcu()->getId()]);
        }

        return $this->render('admin/ecu/software/edit.html.twig', [
            'form' => $form,
            'ecuSoftware' => $ecuSoftware,
            'menuItem' => 'ecu',
        ]);
    }
}