<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    public function __construct(){
        $date =new \DateTime('now');
       }
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $circuit_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_reservation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cart_id")
     */
    private $user_id;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reserv;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $Rsv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCircuitName(): ?string
    {
        return $this->circuit_name;
    }

    public function setCircuitName(string $circuit_name): self
    {
        $this->circuit_name = $circuit_name;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
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

    public function getCreatedAt(): ?\DateTime
    {
        
        return $this->created_at; 
        
    
    }

    public function setCreatedAt(\DateTime $created_at ): self
    {
        
        $this->created_at = $created_at;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(?\DateTimeInterface $date_reservation): self
    {
        $this->date_reservation = $date_reservation;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;
        

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReserv(): ?string
    {
        return $this->reserv;
    }

    public function setReserv(?string $reserv): self
    {
        $this->reserv = $reserv;

        return $this;
    }

    public function getRsv(): ?\DateTimeInterface
    {
        return $this->Rsv;
    }

    public function setRsv(?\DateTimeInterface $Rsv): self
    {
        $this->Rsv = $Rsv;

        return $this;
    }
}
