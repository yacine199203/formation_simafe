<?php

namespace App\Entity;

use App\Repository\ConditionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConditionRepository::class)
 * @ORM\Table(name="`condition`")
 */
class Condition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Recruitement::class, inversedBy="conditions")
     */
    private $recruitement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $liste;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecruitement(): ?Recruitement
    {
        return $this->recruitement;
    }

    public function setRecruitement(?Recruitement $recruitement): self
    {
        $this->recruitement = $recruitement;

        return $this;
    }

    public function getListe(): ?string
    {
        return $this->liste;
    }

    public function setListe(string $liste): self
    {
        $this->liste = ucfirst(mb_strtolower($liste, 'UTF-8'));


        return $this;
    }
}
