<?php

namespace App\Controller\Api\Ecu\Software;

use App\Entity\EcuSoftware;
use App\Repository\EcuSoftwareRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/ecu/software/autocomplete', name: 'api_ecu_software_autocomplete', methods: [Request::METHOD_GET])]
class SoftwareAutocompleteAction extends AbstractController
{
    public function __invoke(Request $request, EcuSoftwareRepository $repository): JsonResponse
    {
        if ((null === $search = $request->get('query')) || false === is_scalar($search) || '' === (string) $search) {
            throw new BadRequestHttpException('Missed mandatory variable `query`');
        }

        if ((null === $ecuId = $request->get('ecuId')) || false === is_scalar($ecuId) || '' === (string) $ecuId) {
            throw new BadRequestHttpException('Missed mandatory variable `ecuId`');
        }

        $queryBuilder = $repository
            ->createQueryBuilder('software')
            ->setMaxResults(5)
            ->andWhere('lower(software.version) like :search')
            ->setParameter('search', sprintf('%%%s%%', mb_strtolower((string) $search)))
            ->andWhere('IDENTITY(software.ecu) = :id')
            ->setParameter('id', $ecuId);

        $data = [];
        foreach ($queryBuilder->getQuery()->getResult() as $record) {
            if (EcuSoftware::VERSION_ANY === $record->getVersion()) {
                continue;
            }
            $data[] = [
                'value' => $record->getId(),
                'label' => $record->getVersion(),
            ];
        }

        return $this->json($data);
    }
}