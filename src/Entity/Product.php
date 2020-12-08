<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\Table(name="Product", indexes={@ORM\Index(columns={"product_name"}, flags={"fulltext"})})
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"productName"},
 * message="Ce produit existe déja"
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $productName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $png;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pdf;

    /**
     * @ORM\OneToMany(targetEntity=Characteristics::class, mappedBy="product", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $characteristics;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=JobProduct::class, mappedBy="product", orphanRemoval=true,cascade={"persist"})
     * @Assert\Valid()
     */
    private $jobProducts;

    public function __construct()
    {
        $this->characteristics = new ArrayCollection();
        $this->jobProducts = new ArrayCollection();
    }

    /** 
    *@ORM\PrePersist
    *@ORM\PreUpdate
    *@return void 
    */
    public function intialSlug(){
        if(empty($this->slug) || !empty($this->slug)){
            $slugify= new Slugify();
            $this->slug = $slugify->slugify($this->productName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = ucfirst(mb_strtolower($productName, 'UTF-8'));

        return $this;
    }

    public function getPng()
    {
        return $this->png;
    }

    public function setPng($png): self
    {
        $this->png = $png;

        return $this;
    }

    public function getPdf()
    {
        return $this->pdf;
    }

    public function setPdf($pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * @return Collection|Characteristics[]
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    public function addCharacteristic(Characteristics $characteristic): self
    {
        if (!$this->characteristics->contains($characteristic)) {
            $this->characteristics[] = $characteristic;
            $characteristic->setProduct($this);
        }

        return $this;
    }

    public function removeCharacteristic(Characteristics $characteristic): self
    {
        if ($this->characteristics->removeElement($characteristic)) {
            // set the owning side to null (unless already changed)
            if ($characteristic->getProduct() === $this) {
                $characteristic->setProduct(null);
            }
        }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|JobProduct[]
     */
    public function getJobProducts(): Collection
    {
        return $this->jobProducts;
    }
    public function __toString()
{
    return (string) $this->getJobProducts();
}
    public function addJobProduct(JobProduct $jobProduct): self
    {
        if (!$this->jobProducts->contains($jobProduct)) {
            $this->jobProducts[] = $jobProduct;
            $jobProduct->setProduct($this);
        }

        return $this;
    }

    public function removeJobProduct(JobProduct $jobProduct): self
    {
        if ($this->jobProducts->removeElement($jobProduct)) {
            // set the owning side to null (unless already changed)
            if ($jobProduct->getProduct() === $this) {
                $jobProduct->setProduct(null);
            }
        }

        return $this;
    }
    
}
