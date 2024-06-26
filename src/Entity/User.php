<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 255)]
    private $last_name;

    #[ORM\Column(type: 'boolean')]
    private $active;

    #[ORM\ManyToOne(targetEntity: Employ::class, inversedBy: 'users')]
    private $employ;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WorkTime::class)]
    private $workTimes;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private $costHour;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comments;

    #[ORM\Column(type: 'string', length: 15, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $erp_num_mag;

    #[ORM\OneToMany(mappedBy: 'id_user', targetEntity: UserQualification::class)]
    private $userQualifications;

    public function __construct()
    {
        $this->workTimes = new ArrayCollection();
        $this->userQualifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

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

    public function getEmploy(): ?Employ
    {
        return $this->employ;
    }

    public function setEmploy(?Employ $employ): self
    {
        $this->employ = $employ;

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
            $workTime->setUser($this);
        }

        return $this;
    }

    public function removeWorkTime(WorkTime $workTime): self
    {
        if ($this->workTimes->removeElement($workTime)) {
            // set the owning side to null (unless already changed)
            if ($workTime->getUser() === $this) {
                $workTime->setUser(null);
            }
        }

        return $this;
    }

    public function getCostHour(): ?string
    {
        return $this->costHour;
    }

    public function setCostHour(?string $costHour): self
    {
        $this->costHour = $costHour;

        return $this;
    }

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getErpNumMag(): ?int
    {
        return $this->erp_num_mag;
    }

    public function setErpNumMag(?int $erp_num_mag): self
    {
        $this->erp_num_mag = $erp_num_mag;

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
            $userQualification->setUser($this);
        }

        return $this;
    }

    public function removeUserQualification(UserQualification $userQualification): self
    {
        if ($this->userQualifications->removeElement($userQualification)) {
            // set the owning side to null (unless already changed)
            if ($userQualification->getUser() === $this) {
                $userQualification->setUser(null);
            }
        }

        return $this;
    }
}
