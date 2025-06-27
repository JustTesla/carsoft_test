<?php

namespace App\Controller\Admin\Ecu\Software\Service;

use App\Replacement\Replacement;
use App\Repository\EcuSoftwareServiceRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/ecu/software/service/{ecuSoftwareServiceId}/edit', name: 'admin_ecu_software_service_edit', requirements: ['ecuSoftwareServiceId' => Requirement::UUID_V4])]
class EditServiceAction extends AbstractController
{
    public function __invoke(
        string $ecuSoftwareServiceId,
        Request $request,
        EntityManagerInterface $entityManager,
        EcuSoftwareServiceRepository $repository,
        Filesystem $filesystem,
        Replacement $replacement
    ): Response {
        if (null === $softwareService = $repository->find($ecuSoftwareServiceId)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder(['service' => $softwareService->getService()->getName()])
            ->setAction(
                $this->generateUrl('admin_ecu_software_service_edit', ['ecuSoftwareServiceId' => $ecuSoftwareServiceId])
            )
            ->setMethod('POST')
            ->add('service', TextType::class, ['disabled' => true])
            ->add('replacement', FileType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $softwareService->setReplacement($replacement->parseFile($file = $form->get('replacement')->getData()));
                $filesystem->remove($file);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/software/service/edit.html.twig', [
                    'form' => $form,
                    'softwareService' => $softwareService,
                    'menuItem' => 'ecu',
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_software_details', ['id' => $softwareService->getEcuSoftware()->getId()]);
        }

        return $this->render('admin/ecu/software/service/edit.html.twig', [
            'form' => $form,
            'softwareService' => $softwareService,
        ]);
    }
}