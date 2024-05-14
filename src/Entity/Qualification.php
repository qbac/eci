<?php

namespace App\Entity;

use App\Repository\QualificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QualificationRepository::class)]
class Qualification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $remind_days_before;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\OneToMany(mappedBy: 'qualification', targetEntity: UserQualification::class)]
    private $userQualifications;

    public function __construct()
    {
        $this->userQualifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getRemindDaysBefore(): ?int
    {
        return $this->remind_days_before;
    }

    public function setRemindDaysBefore(?int $remind_days_before): self
    {
        $this->remind_days_before = $remind_days_before;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, UserQualification>
     */
    public function getUserQualifications(): Collection
    {
        return $this->userQualifications;
    }

    public function addUserQualification(UserQualification $userQualification): self
    {
        if (!$this->userQualifications->contains($userQualification)) {
            $this->userQualifications[] = $userQualification;
            $userQualification->setQualification($this);
        }

        return $this;
    }

    public function removeUserQualification(UserQualification $userQualification): self
    {
        if ($this->userQualifications->removeElement($userQualification)) {
            // set the owning side to null (unless already changed)
            if ($userQualification->getQualification() === $this) {
                $userQualification->setQualification(null);
            }
        }

        return $this;
    }
}
