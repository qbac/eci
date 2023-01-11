<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $cost_hour;

    #[ORM\Column(type: 'time')]
    private $work_start;

    #[ORM\Column(type: 'time')]
    private $work_end;

    #[ORM\Column(type: 'time')]
    private $travel_time;

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

    public function getCostHour(): ?string
    {
        return $this->cost_hour;
    }

    public function setCostHour(?string $cost_hour): self
    {
        $this->cost_hour = $cost_hour;

        return $this;
    }

    public function getWorkStart(): ?\DateTimeInterface
    {
        return $this->work_start;
    }

    public function setWorkStart(\DateTimeInterface $work_start): self
    {
        $this->work_start = $work_start;

        return $this;
    }

    public function getWorkEnd(): ?\DateTimeInterface
    {
        return $this->work_end;
    }

    public function setWorkEnd(\DateTimeInterface $work_end): self
    {
        $this->work_end = $work_end;

        return $this;
    }

    public function getTravelTime(): ?\DateTimeInterface
    {
        return $this->travel_time;
    }

    public function setTravelTime(\DateTimeInterface $travel_time): self
    {
        $this->travel_time = $travel_time;

        return $this;
    }

    /**
    * @Assert\Callback
    */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->work_start > $this->work_end) {
            $context->buildViolation('Godzina zakończenia musi być większa od godziny rozpoczęcia')
            ->addViolation();
        }
    }
}
