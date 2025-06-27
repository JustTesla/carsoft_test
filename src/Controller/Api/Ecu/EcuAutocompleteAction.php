<?php

namespace App\Controller\Api\Ecu;

use App\Repository\EcuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/ecu/autocomplete', name: 'api_ecu_autocomplete', methods: [Request::METHOD_GET])]
class EcuAutocompleteAction extends AbstractController
{
    public function __invoke(Request $request, EcuRepository $repository): JsonResponse
    {
        if ((null === $search = $request->get('query')) || false === is_scalar($search)) {
            throw new BadRequestHttpException('Missed mandatory variable `query`');
        }

        $queryBuilder = $repository
            ->createQueryBuilder('ecu')
            ->setMaxResults(5)
            ->andWhere('lower(ecu.name) like :search')
            ->setParameter('search', sprintf('%%%s%%', mb_strtolower((string) $search)));

        $data = [];
        foreach ($queryBuilder->getQuery()->getResult() as $record) {
            $data[] = [
                'value' => $record->getId(),
                'label' => $record->getName(),
            ];
        }

        return $this->json($data);
    }
}