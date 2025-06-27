<?php

declare(strict_types=1);

namespace App\Controller\Api\Ecu\Software\Service;

use App\Entity\EcuSoftware;
use App\Repository\EcuSoftwareRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/ecu/software/service/autocomplete', name: 'api_ecu_software_service_autocomplete', methods: [Request::METHOD_GET])]
class ServiceAutocomplete extends AbstractController
{
    public function __invoke(Request $request, ServiceRepository $repository, EcuSoftwareRepository $softwareRepository): JsonResponse
    {
        if ((null === $search = $request->get('query')) || false === is_scalar($search) || '' === (string) $search) {
            throw new BadRequestHttpException('Missed mandatory variable `query`');
        }

        if ((null === $softwareId = $request->get('softwareId')) || false === is_scalar($softwareId) || '' === (string) $softwareId) {
            throw new BadRequestHttpException('Missed mandatory variable `softwareId`');
        }

        $softwareData = explode(':', $softwareId);
        if (2 === count($softwareData)) {
            [$type, $ecuId] = $softwareData;
            if ('any' === $type) {
                $softwareId = $softwareRepository->findOneBy(['version' => EcuSoftware::VERSION_ANY, 'ecu' => $ecuId])->getId();
            }
        }

        $queryBuilder = $repository
            ->createQueryBuilder('service')
            ->leftJoin('service.ecuSoftwareServices', 'ecuSoftwareServices')
            ->setMaxResults(5)
            ->andWhere('lower(service.name) like :search')
            ->setParameter('search', sprintf('%%%s%%', mb_strtolower((string) $search)))
            ->andWhere('IDENTITY(ecuSoftwareServices.ecuSoftware) = :id')
            ->setParameter('id', $softwareId);

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
