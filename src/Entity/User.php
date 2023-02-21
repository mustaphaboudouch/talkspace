<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePicture = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $socialNumber = null;

    #[ORM\Column]
    private array $roles = [];
    private string $role = 'ROLE_PATIENT';

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isActive = false;

    #[ORM\OneToOne(inversedBy: 'doctor', cascade: ['persist', 'remove'])]
    private ?Account $account = null;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Schedule::class)]
    private Collection $schedules;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Appointment::class)]
    private Collection $doctorAppointments;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: File::class)]
    private Collection $files;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: DayOff::class)]
    private Collection $daysOff;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->doctorAppointments = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->daysOff = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getSocialNumber(): ?string
    {
        return $this->socialNumber;
    }

    public function setSocialNumber(?string $socialNumber): self
    {
        $this->socialNumber = $socialNumber;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

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
     * Make sure that user can have only one role
     *
     * @see UserInterface
     */

    public function getRole(): string
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_PATIENT';

        return $roles[0];
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_PATIENT';

        return array_slice($roles, 0, 1);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_slice($roles, 0, 1);

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

    /**
     * @return Collection<int, Schedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(Schedule $schedule): self
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules->add($schedule);
            $schedule->setDoctor($this);
        }

        return $this;
    }

    public function removeSchedule(Schedule $schedule): self
    {
        if ($this->schedules->removeElement($schedule)) {
            // set the owning side to null (unless already changed)
            if ($schedule->getDoctor() === $this) {
                $schedule->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setPatient($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getPatient() === $this) {
                $appointment->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getDoctorAppointments(): Collection
    {
        return $this->doctorAppointments;
    }

    public function addDoctorAppointment(Appointment $doctorAppointment): self
    {
        if (!$this->doctorAppointments->contains($doctorAppointment)) {
            $this->doctorAppointments->add($doctorAppointment);
            $doctorAppointment->setDoctor($this);
        }

        return $this;
    }

    public function removeDoctorAppointment(Appointment $doctorAppointment): self
    {
        if ($this->doctorAppointments->removeElement($doctorAppointment)) {
            // set the owning side to null (unless already changed)
            if ($doctorAppointment->getDoctor() === $this) {
                $doctorAppointment->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setDoctor($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getDoctor() === $this) {
                $service->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
            $file->setSender($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getSender() === $this) {
                $file->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DayOff>
     */
    public function getDaysOff(): Collection
    {
        return $this->daysOff;
    }

    public function addDaysOff(DayOff $daysOff): self
    {
        if (!$this->daysOff->contains($daysOff)) {
            $this->daysOff->add($daysOff);
            $daysOff->setDoctor($this);
        }

        return $this;
    }

    public function removeDaysOff(DayOff $daysOff): self
    {
        if ($this->daysOff->removeElement($daysOff)) {
            // set the owning side to null (unless already changed)
            if ($daysOff->getDoctor() === $this) {
                $daysOff->setDoctor(null);
            }
        }

        return $this;
    }
}
