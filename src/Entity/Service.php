<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $name;

    #[ORM\OneToMany(targetEntity: EcuSoftwareService::class, mappedBy: 'service')]
    private Collection $ecuSoftwareServices;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->ecuSoftwareServices = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEcuSoftwareServices(): Collection
    {
        return $this->ecuSoftwareServices;
    }
}
