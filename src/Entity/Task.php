<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $convert_format = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resize_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resize_param = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cached_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $output_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hash = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getConvertFormat(): ?string
    {
        return $this->convert_format;
    }

    public function setConvertFormat(?string $convert_format): static
    {
        $this->convert_format = $convert_format;

        return $this;
    }

    public function getResizeType(): ?string
    {
        return $this->resize_type;
    }

    public function setResizeType(?string $resize_type): static
    {
        $this->resize_type = $resize_type;

        return $this;
    }

    public function getResizeParam(): ?string
    {
        return $this->resize_param;
    }

    public function setResizeParam(?string $resize_param): static
    {
        $this->resize_param = $resize_param;

        return $this;
    }

    public function getCachedName(): ?string
    {
        return $this->cached_name;
    }

    public function setCachedName(?string $cached_name): static
    {
        $this->cached_name = $cached_name;

        return $this;
    }

    public function getOutputName(): ?string
    {
        return $this->output_name;
    }

    public function setOutputName(?string $output_name): static
    {
        $this->output_name = $output_name;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }
}
