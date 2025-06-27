<?php

namespace App\Controller\Admin;

use App\Datatable\DatatableRequest;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

class ServiceController extends AbstractController
{
    public function __construct(private ServiceRepository $repository)
    {
    }

    #[Route('/admin/service', name: 'admin_service')]
    public function index(): Response
    {
        return $this->render('admin/service/index.html.twig');
    }

    #[Route('/admin/service/datatable', name: 'admin_service_datatable')]
    public function dataTable(Request $request, DenormalizerInterface $denormalizer): JsonResponse
    {
        $listRequest = $denormalizer->denormalize($request->query->all(), DatatableRequest::class, 'json');

        $queryBuilder = $this->repository
            ->createQueryBuilder('service')
            ->setMaxResults($listRequest->limit)
            ->setFirstResult($listRequest->offset);

        if (true === isset($listRequest->search)) {
            $queryBuilder
                ->andWhere('lower(service.name) like :search')
                ->setParameter('search', sprintf('%%%s%%', mb_strtolower($listRequest->search)));
        }

        if (true === isset($listRequest->sort[0])) {
            $queryBuilder->addOrderBy('service.name', $listRequest->sort[0]);
        }

        $paginator = new Paginator($queryBuilder->getQuery());
        
        $data = [];
        foreach ($paginator as $record) {
            $data[] = [
                $record->getName(),
                [
                    $this->generateUrl('admin_service_edit', ['id' => $record->getId()]),
                    $this->generateUrl('admin_service_delete', ['id' => $record->getId()])
                ]
            ];
        }

        return $this->json([
            'data' => $data,
            'draw' => $listRequest->draw,
            'recordsFiltered' => count($paginator),
            'recordsTotal' => $this->repository->count()
        ]);
    }

    #[Route('/admin/service/new', name: 'admin_service_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder(new Service((string) Uuid::v4()))
            ->setAction($this->generateUrl('admin_service_new'))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($form->getData());
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/service/new.html.twig', [
                    'form' => $form,
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_service');
        }

        return $this->render('admin/service/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/service/edit/{id}', name: 'admin_service_edit')]
    public function edit(string $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (null === $service = $this->repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormBuilder($service)
            ->setAction($this->generateUrl('admin_service_edit', ['id' => $id]))
            ->setMethod('POST')
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $exception) {
                return $this->render('admin/service/edit.html.twig', [
                    'form' => $form,
                    'error' => $exception->getMessage()
                ]);
            }

            return $this->redirectToRoute('admin_service');
        }

        return $this->render('admin/service/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/admin/service/delete/{id}', name: 'admin_service_delete')]
    public function delete(string $id, EntityManagerInterface $entityManager): Response
    {
        if (null === $service = $this->repository->find($id)) {
            throw $this->createNotFoundException();
        }

        $entityManager->remove($service);
        $entityManager->flush();

        return $this->redirectToRoute('admin_service');
    }
}