<?php

namespace App\Controller\Admin\Ecu\Software\Service;

use App\Datatable\DatatableRequest;
use App\Repository\EcuSoftwareServiceRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[Route('/admin/ecu/software/{ecuSoftwareId}/service/datatable', name: 'admin_ecu_software_service_datatable', requirements: ['ecuSoftwareId' => Requirement::UUID_V4])]
class ServiceDatatableAction extends AbstractController
{
    public function __invoke(
        string $ecuSoftwareId,
        Request $request,
        DenormalizerInterface $denormalizer,
        EcuSoftwareServiceRepository $repository
    ): JsonResponse {
        $listRequest = $denormalizer->denormalize($request->query->all(), DatatableRequest::class, 'json');

        $queryBuilder = $repository
            ->createQueryBuilder('ecuSoftwareService')
            ->leftJoin('ecuSoftwareService.ecuSoftware', 'ecuSoftware')
            ->leftJoin('ecuSoftwareService.service', 'service')
            ->setMaxResults($listRequest->limit)
            ->setFirstResult($listRequest->offset)
            ->andWhere('IDENTITY(ecuSoftwareService.ecuSoftware) = :id')
            ->setParameter('id', $ecuSoftwareId);

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
                $record->getService()->getName(),
                count($record->getReplacement()),
                [
                    $this->generateUrl('admin_ecu_software_service_edit', ['ecuSoftwareServiceId' => $record->getId()]),
                    $this->generateUrl('admin_ecu_software_service_delete', ['ecuSoftwareServiceId' => $record->getId()]),
                ]
            ];
        }

        return $this->json([
            'data' => $data,
            'draw' => $listRequest->draw,
            'recordsFiltered' => count($paginator),
            'recordsTotal' => $repository->count(['ecuSoftware' => $ecuSoftwareId]),
        ]);
    }
}