<?php

namespace App\Controller\Admin\Ecu;

use App\Datatable\DatatableRequest;
use App\Entity\EcuSoftware;
use App\Repository\EcuRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[Route('/admin/ecu/datatable', name: 'admin_ecu_datatable')]
class EcuDatatableAction extends AbstractController
{
    public function __invoke(Request $request, DenormalizerInterface $denormalizer, EcuRepository $repository): JsonResponse
    {
        $listRequest = $denormalizer->denormalize($request->query->all(), DatatableRequest::class, 'json');

        $queryBuilder = $repository
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
            'recordsTotal' => $repository->count(),
        ]);
    }
}