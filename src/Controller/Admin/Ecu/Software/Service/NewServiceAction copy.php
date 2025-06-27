<?php

namespace App\Controller\Admin\Ecu\Software\Service;

use App\Entity\EcuSoftwareService;
use App\Replacement\Replacement;
use App\Repository\EcuSoftwareRepository;
use App\Repository\ServiceRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

#[Route('/admin/ecu/software/{ecuSoftwareId}/service/new', name: 'admin_ecu_software_service_new', requirements: ['ecuSoftwareId' => Requirement::UUID_V4])]
class NewServiceAction extends AbstractController
{
    public function __invoke(
        string $ecuSoftwareId,
        Request $request,
        EntityManagerInterface $entityManager,
        EcuSoftwareRepository $softwareRepository,
        ServiceRepository $serviceRepository,
        Filesystem $filesystem,
        Replacement $replacement
    ): Response {
        if (null === $software = $softwareRepository->find($ecuSoftwareId)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('admin_ecu_software_service_new', ['ecuSoftwareId' => $ecuSoftwareId]))
            ->setMethod('POST')
            ->add('service', ChoiceType::class, ['choices' => $serviceRepository->getChoices($ecuSoftwareId)])
            ->add('replacement', FileType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist(
                    new EcuSoftwareService(
                        (string) Uuid::v4(),
                        $software,
                        $form->get('service')->getData(),
                        $replacement->parseFile($file = $form->get('replacement')->getData())
                    )
                );
                $filesystem->remove($file);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/software/service/new.html.twig', [
                    'form' => $form,
                    'software' => $software,
                    'menuItem' => 'ecu',
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_software_details', ['id' => $ecuSoftwareId]);
        }

        return $this->render('admin/ecu/software/service/new.html.twig', [
            'form' => $form,
            'software' => $software,
        ]);
    }
}