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

    #[ORM\Column(type: 'integer', nullable: true)]
    private $number_zlec;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $kod_zlec;

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

    public function getNumberZlec(): ?int
    {
        return $this->number_zlec;
    }

    public function setNumberZlec(?int $number_zlec): self
    {
        $this->number_zlec = $number_zlec;

        return $this;
    }

    public function getKodZlec(): ?string
    {
        return $this->kod_zlec;
    }

    public function setKodZlec(?string $kod_zlec): self
    {
        $this->kod_zlec = $kod_zlec;

        return $this;
    }
}
