<?php

namespace App\Controller\Admin;

use App\Datatable\DatatableRequest;
use App\Entity\Ecu;
use App\Entity\EcuSoftware;
use App\Repository\EcuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

class EcuController extends AbstractController
{
    public function __construct(private EcuRepository $repository)
    {
    }

    #[Route('/admin/ecu', name: 'admin_ecu')]
    public function index(): Response
    {
        return $this->render('admin/ecu/index.html.twig', ['menuItem' => 'ecu']);
    }

    #[Route('/admin/ecu/datatable', name: 'admin_ecu_datatable')]
    public function dataTable(Request $request, DenormalizerInterface $denormalizer): JsonResponse
    {
        $listRequest = $denormalizer->denormalize($request->query->all(), DatatableRequest::class, 'json');

        $queryBuilder = $this->repository
            ->createQueryBuilder('ecu')
            ->select('ecu', 'ecuSoftwares')
            ->setMaxResults($listRequest->limit)
            ->setFirstResult($listRequest->offset)
            ->leftJoin('ecu.ecuSoftwares', 'ecuSoftwares');

        if (true === isset($listRequest->search)) {
            $queryBuilder
                ->andWhere('lower(ecu.name) like :search')
                ->setParameter('search', sprintf('%%%s%%', mb_strtolower($listRequest->search)));
        }

        if (true === isset($listRequest->sort[0])) {
            $queryBuilder->addOrderBy('ecu.name', $listRequest->sort[0]);
        }

        $paginator = new Paginator($queryBuilder->getQuery());

        $data = [];
        foreach ($paginator as $record) {
            $softwares = [];
            foreach ($record->getEcuSoftwares() as $software) {
                if (EcuSoftware::VERSION_ANY === $software->getVersion()) {
                    continue;
                }
                $softwares[] = $software->getVersion();
            }

            $data[] = [
                $record->getName(),
                $softwares,
                [
                    $this->generateUrl('admin_ecu_edit', ['id' => $record->getId()]),
                    $this->generateUrl('admin_ecu_delete', ['id' => $record->getId()]),
                    $this->generateUrl('admin_ecu_details', ['id' => $record->getId()]),
                ]
            ];
        }

        return $this->json([
            'data' => $data,
            'draw' => $listRequest->draw,
            'recordsFiltered' => count($paginator),
            'recordsTotal' => $this->repository->count(),
        ]);
    }

    #[Route('/admin/ecu/new', name: 'admin_ecu_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder(new Ecu($id = (string) Uuid::v4()))
            ->setAction($this->generateUrl('admin_ecu_new'))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->beginTransaction();
            $entityManager->persist($ecu = $form->getData());
            $entityManager->persist(new EcuSoftware((string)Uuid::v4(), $ecu));
            $entityManager->flush();
            $entityManager->commit();

            return $this->redirectToRoute('admin_ecu_details', ['id' => $id]);
        }

        return $this->render('admin/ecu/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/ecu/{id}/edit', name: 'admin_ecu_edit', requirements: ['id' => Requirement::UUID_V4])]
    public function edit(string $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null === $ecu = $this->repository->find($id)) {
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
            $entityManager->flush();

            return $this->redirectToRoute('admin_ecu_details', ['id' => $id]);
        }

        return $this->render('admin/ecu/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/ecu/{id}/delete', name: 'admin_ecu_delete', requirements: ['id' => Requirement::UUID_V4])]
    public function delete(string $id, EntityManagerInterface $entityManager): Response
    {
        if (null === $ecu = $this->repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($ecu);
        $entityManager->flush();

        return $this->redirectToRoute('admin_ecu');
    }

    #[Route('/admin/ecu/{id}', name: 'admin_ecu_details', requirements: ['id' => Requirement::UUID_V4])]
    public function details(string $id): Response
    {
        if (null === $ecu = $this->repository->find($id)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'admin/ecu/details.html.twig', 
            [
                'ecu' => $this->repository->find($id),
            ]
        );
    }
}