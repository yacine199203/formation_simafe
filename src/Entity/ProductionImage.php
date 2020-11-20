<?php

namespace App\Entity;

use App\Repository\ProductionImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductionImageRepository::class)
 */
class ProductionImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ProductionJob::class, inversedBy="productionImages")
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $image;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?ProductionJob
    {
        return $this->customer;
    }

    public function setCustomer(?ProductionJob $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }
}
