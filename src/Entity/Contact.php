<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
 
    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $name;

    /**
     * @Assert\Email(message="Ce champ doit être un email")
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Ce champ est vide")
     */
    private $message;

   
    private $file;

 
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
