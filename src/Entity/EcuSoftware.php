<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EcuSoftwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EcuSoftwareRepository::class)]
#[ORM\UniqueConstraint(fields: ['ecu', 'version'])]
class EcuSoftware
{
    public const VERSION_ANY = '*';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Ecu $ecu;

    #[ORM\Column(nullable: false)]
    private string $version;

    #[ORM\OneToMany(targetEntity: EcuSoftwareService::class, mappedBy: 'ecuSoftware')]
    private Collection $ecuSoftwareServices;

    public function __construct(string $id, Ecu $ecu, string $version = self::VERSION_ANY)
    {
        $this->id = $id;
        $this->ecu = $ecu;
        $this->version = $version;
        $this->ecuSoftwareServices = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEcu(): Ecu
    {
        return $this->ecu;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getEcuSoftwareServices(): Collection
    {
        return $this->ecuSoftwareServices;
    }
}