<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 */
class Users
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $Objects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Objects", mappedBy="user", orphanRemoval=true)
     */
    private $GaveObject;

    public function __construct()
    {
        $this->GaveObject = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): self
    {
        $this->Username = $Username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): self
    {
        $this->Password = $Password;

        return $this;
    }

    public function getObjects(): ?string
    {
        return $this->Objects;
    }

    public function setObjects(?string $Objects): self
    {
        $this->Objects = $Objects;

        return $this;
    }

    /**
     * @return Collection|Objects[]
     */
    public function getGaveObject(): Collection
    {
        return $this->GaveObject;
    }

    public function addGaveObject(Objects $gaveObject): self
    {
        if (!$this->GaveObject->contains($gaveObject)) {
            $this->GaveObject[] = $gaveObject;
            $gaveObject->setUser($this);
        }

        return $this;
    }

    public function removeGaveObject(Objects $gaveObject): self
    {
        if ($this->GaveObject->contains($gaveObject)) {
            $this->GaveObject->removeElement($gaveObject);
            // set the owning side to null (unless already changed)
            if ($gaveObject->getUser() === $this) {
                $gaveObject->setUser(null);
            }
        }

        return $this;
    }
}
