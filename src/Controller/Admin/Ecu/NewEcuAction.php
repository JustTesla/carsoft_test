<?php

namespace App\Controller\Admin\Ecu;

use App\Entity\Ecu;
use App\Entity\EcuSoftware;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/admin/ecu/new', name: 'admin_ecu_new')]
class NewEcuAction extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder(new Ecu($id = (string) Uuid::v4()))
            ->setAction($this->generateUrl('admin_ecu_new'))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->beginTransaction();
                $entityManager->persist($ecu = $form->getData());
                $entityManager->persist(new EcuSoftware((string)Uuid::v4(), $ecu));
                $entityManager->flush();
                $entityManager->commit();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/ecu/new.html.twig', [
                    'form' => $form,
                    'menuItem' => 'ecu',
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_ecu_details', ['id' => $id]);
        }

        return $this->render('admin/ecu/new.html.twig', [
            'form' => $form,
        ]);
    }
}