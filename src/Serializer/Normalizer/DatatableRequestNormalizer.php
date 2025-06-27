<?php

declare(strict_types=1);

namespace App\Serializer\Normalizer;

use App\Datatable\DatatableRequest;
use Iterator;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DatatableRequestNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return new DatatableRequest(
            (int)$data['length'], 
            (int)$data['start'], 
            (string)$data['search']['value'], 
            iterator_to_array($this->denormalizeSortFields($data['order'])),
            (int)$data['draw']
        );
    }

    private function denormalizeSortFields(array $data): Iterator
    {
        foreach ($data as $item) {
            if (false === isset($item['dir'])) {
                continue;
            }
            
            yield (int)$item['column'] => (string)$item['dir'];
        }
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === DatatableRequest::class && false === array_key_exists('list_request', $context);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DatatableRequest::class => true
        ];
    }
}
