<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(name: 'raisonSociale', length: 255, unique: true)]
    private string $raisonSociale;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Expediteur::class)]
    private Collection $expediteurs;

    public function __construct()
    {
        $this->expediteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): self
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * @return Collection<int, Expediteur>
     */
    public function getExpediteurs(): Collection
    {
        return $this->expediteurs;
    }

    public function addExpediteur(Expediteur $expediteur): self
    {
        if (!$this->expediteurs->contains($expediteur)) {
            $this->expediteurs->add($expediteur);
            $expediteur->setClient($this);
        }

        return $this;
    }

    public function removeExpediteur(Expediteur $expediteur): self
    {
        if ($this->expediteurs->removeElement($expediteur)) {
            // set the owning side to null (unless already changed)
            if ($expediteur->getClient() === $this) {
                $expediteur->setClient(null);
            }
        }

        return $this;
    }
}
