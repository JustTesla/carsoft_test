<?php

namespace App\Controller\Admin\Ecu\Software;

use App\Entity\EcuSoftware;
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
use Symfony\Component\Uid\Uuid;

#[Route('/admin/ecu/{ecuId}/software/new', name: 'admin_ecu_software_new', requirements: ['ecuId' => Requirement::UUID_V4])]
class NewSoftwareAction extends AbstractController
{
    public function __invoke(
        string $ecuId,
        Request $request,
        EntityManagerInterface $entityManager,
        EcuRepository $ecuRepository
    ): Response {
        if (null === $ecu = $ecuRepository->find($ecuId)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('admin_ecu_software_new', ['ecuId' => $ecuId]))
            ->setMethod('POST')
            ->add('version', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist(
                    new EcuSoftware(
                        (string) Uuid::v4(),
                        $ecu,
                        $form->get('version')->getData(),
                    )
                );
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/software/new.html.twig', [
                    'form' => $form,
                    'ecu' => $ecu,
                    'menuItem' => 'ecu',
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_details', ['id' => $ecuId]);
        }

        return $this->render('admin/ecu/software/new.html.twig', [
            'form' => $form,
            'ecu' => $ecu,
            'menuItem' => 'ecu',
        ]);
    }
}