<?php

namespace App\Controller\Admin\Ecu\Software;

use App\Datatable\DatatableRequest;
use App\Repository\EcuSoftwareRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[Route('/admin/ecu/{ecuId}/software/datatable', name: 'admin_ecu_software_datatable', requirements: ['ecuId' => Requirement::UUID_V4])]
class SoftwareDatatableAction extends AbstractController
{
    public function __invoke(
        string $ecuId,
        Request $request,
        DenormalizerInterface $denormalizer,
        EcuSoftwareRepository $ecuSoftwareRepository
    ): JsonResponse {
        $listRequest = $denormalizer->denormalize($request->query->all(), DatatableRequest::class, 'json');

        $queryBuilder = $ecuSoftwareRepository
            ->createQueryBuilder('ecuSoftware')
            ->leftJoin('ecuSoftware.ecuSoftwareServices', 'ecuSoftwareServices')
            ->leftJoin('ecuSoftwareServices.service', 'service')
            ->setMaxResults($listRequest->limit)
            ->setFirstResult($listRequest->offset)
            ->andWhere('IDENTITY(ecuSoftware.ecu) = :id')
            ->setParameter('id', $ecuId);

        if (true === isset($listRequest->search)) {
            $queryBuilder
                ->andWhere('lower(ecuSoftware.version) like :search')
                ->setParameter('search', sprintf('%%%s%%', mb_strtolower($listRequest->search)));
        }

        if (true === isset($listRequest->sort[0])) {
            $queryBuilder->addOrderBy('ecuSoftware.version', $listRequest->sort[0]);
        }

        $paginator = new Paginator($queryBuilder->getQuery());

        $data = [];
        foreach ($paginator as $record) {
            $services = [];
            foreach ($record->getEcuSoftwareServices() as $service) {
                $services[] = $service->getService()->getName();
            }

            $data[] = [
                $record->getVersion(),
                $services,
                [
                    $this->generateUrl('admin_ecu_software_edit', ['id' => $record->getId()]),
                    $this->generateUrl('admin_ecu_software_delete', ['id' => $record->getId()]),
                    $this->generateUrl('admin_ecu_software_details', ['id' => $record->getId()])
                ]
            ];
        }

        return $this->json([
            'data' => $data,
            'draw' => $listRequest->draw,
            'recordsFiltered' => count($paginator),
            'recordsTotal' => $ecuSoftwareRepository->count(['ecu' => $ecuId]),
        ]);
    }
}