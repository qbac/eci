<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
class WorkTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'workTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: 'workTimes')]
    #[ORM\JoinColumn(nullable: false)]
    private $project;

    #[ORM\ManyToOne(targetEntity: Employ::class, inversedBy: 'workTimes')]
    private $employ;

    #[ORM\Column(type: 'date')]
    private $work_date;

    #[ORM\Column(type: 'time')]
    private $work_time;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getEmploy(): ?Employ
    {
        return $this->employ;
    }

    public function setEmploy(?Employ $employ): self
    {
        $this->employ = $employ;

        return $this;
    }

    public function getWorkDate(): ?\DateTimeInterface
    {
        return $this->work_date;
    }

    public function setWorkDate(\DateTimeInterface $work_date): self
    {
        $this->work_date = $work_date;

        return $this;
    }

    public function getWorkTime(): ?\DateTimeInterface
    {
        return $this->work_time;
    }

    public function setWorkTime(\DateTimeInterface $work_time): self
    {
        $this->work_time = $work_time;

        return $this;
    }
}
