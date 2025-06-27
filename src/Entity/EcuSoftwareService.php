<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EcuSoftwareServiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcuSoftwareServiceRepository::class)]
#[ORM\UniqueConstraint(fields: ['ecuSoftware', 'service'])]
class EcuSoftwareService
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private EcuSoftware $ecuSoftware;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Service $service;

    #[ORM\Column(type: 'array')]
    private array $replacement;

    public function __construct(string $id, EcuSoftware $ecuSoftware, Service $service, array $replacement)
    {
        $this->id = $id;
        $this->ecuSoftware = $ecuSoftware;
        $this->service = $service;
        $this->replacement = $replacement;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEcuSoftware(): EcuSoftware
    {
        return $this->ecuSoftware;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getReplacement(): array
    {
        return $this->replacement;
    }

    public function setReplacement(array $replacement): void
    {
        $this->replacement = $replacement;
    }
}