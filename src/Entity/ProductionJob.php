<?php

namespace App\Entity;

use App\Repository\ProductionJobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductionJobRepository::class)
 */
class ProductionJob
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $customer;

    /**
     * @ORM\ManyToOne(targetEntity=Job::class, inversedBy="productionJobs")
     */
    private $job;

    /**
     * @ORM\OneToMany(targetEntity=ProductionImage::class, mappedBy="customer")
     * @Assert\Valid()
     */
    private $productionImages;

    public function __construct()
    {
        $this->productionImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = mb_strtoupper($customer, 'UTF-8');

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Collection|ProductionImage[]
     */
    public function getProductionImages()
    {
        return $this->productionImages;
    }

    public function addProductionImage( $productionImage): self
    {
        if (!$this->productionImages->contains($productionImage)) {
            $this->productionImages[] = $productionImage;
            $productionImage->setCustomer($this);
        }

        return $this;
    }

    public function removeProductionImage(ProductionImage $productionImage): self
    {
        if ($this->productionImages->removeElement($productionImage)) {
            // set the owning side to null (unless already changed)
            if ($productionImage->getCustomer() === $this) {
                $productionImage->setCustomer(null);
            }
        }

        return $this;
    }
}
