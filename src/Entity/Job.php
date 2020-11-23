<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\JobRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 * fields={"job"},
 * message="Ce métier existe déja"
 * )
 */
class Job
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $job;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=JobProduct::class, mappedBy="job",orphanRemoval=true)
     */
    private $jobProducts;

    /**
     * @ORM\OneToMany(targetEntity=ProductionJob::class, mappedBy="job")
     */
    private $productionJobs;


    public function __construct()
    {
        $this->jobProducts = new ArrayCollection();
        $this->productionJobs = new ArrayCollection();
    }

    /** 
    *@ORM\PrePersist
    *@ORM\PreUpdate
    *@return void 
    */
    public function intialSlug(){
        if(empty($this->slug) || !empty($this->slug)){
            $slugify= new Slugify();
            $this->slug = $slugify->slugify($this->job);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): self
    {
        $this->job = mb_strtoupper($job, 'UTF-8');

        return $this;
    }
    public function __toString()
    {
        return $this->job;
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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|JobProduct[]
     */
    public function getJobProducts(): Collection
    {
        return $this->jobProducts;
    }

    public function addJobProduct(JobProduct $jobProduct): self
    {
        if (!$this->jobProducts->contains($jobProduct)) {
            $this->jobProducts[] = $jobProduct;
            $jobProduct->setJob($this);
        }

        return $this;
    }

    public function removeJobProduct(JobProduct $jobProduct): self
    {
        if ($this->jobProducts->removeElement($jobProduct)) {
            // set the owning side to null (unless already changed)
            if ($jobProduct->getJob() === $this) {
                $jobProduct->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductionJob[]
     */
    public function getProductionJobs(): Collection
    {
        return $this->productionJobs;
    }

    public function addProductionJob(ProductionJob $productionJob): self
    {
        if (!$this->productionJobs->contains($productionJob)) {
            $this->productionJobs[] = $productionJob;
            $productionJob->setJob($this);
        }

        return $this;
    }

    public function removeProductionJob(ProductionJob $productionJob): self
    {
        if ($this->productionJobs->removeElement($productionJob)) {
            // set the owning side to null (unless already changed)
            if ($productionJob->getJob() === $this) {
                $productionJob->setJob(null);
            }
        }

        return $this;
    }

}
