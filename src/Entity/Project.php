<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'date')]
    private $date_start;

    #[ORM\Column(type: 'date')]
    private $date_end;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: WorkTime::class)]
    private $workTimes;

    public function __construct()
    {
        $this->workTimes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTimeInterface $date_end): self
    {
        $this->date_end = $date_end;

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
     * @return Collection<int, WorkTime>
     */
    public function getWorkTimes(): Collection
    {
        return $this->workTimes;
    }

    public function addWorkTime(WorkTime $workTime): self
    {
        if (!$this->workTimes->contains($workTime)) {
            $this->workTimes[] = $workTime;
            $workTime->setProject($this);
        }

        return $this;
    }

    public function removeWorkTime(WorkTime $workTime): self
    {
        if ($this->workTimes->removeElement($workTime)) {
            // set the owning side to null (unless already changed)
            if ($workTime->getProject() === $this) {
                $workTime->setProject(null);
            }
        }

        return $this;
    }
}
