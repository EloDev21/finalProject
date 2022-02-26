<?php

namespace App\Entity;
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *  message = " '{{ value }}' n'est pas une adresse e-mail valide. Merci de recommencer."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*\d).{6,}$/i", message="New password is required to be minimum 6 chars in length and to include at least one letter and one number.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Votre prénom doit être de   4  charactères minimum ",
     *      maxMessage = "Votre prénom doit être de   20  charactères maximum ",
     * ) 
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre prénom ne peut contenir un chiffre"
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Votre prénom doit être de   4  charactères minimum ",
     *      maxMessage = "Votre prénom doit être de   20  charactères maximum ",
     * ) 
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Votre nom ne peut contenir un chiffre"
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *      pattern="/^[0-9]{10}/",
     *      match=false,
     *      message="Votre numéro n'est pas valide. Exemple de numéro valide : 0123456789"
     * )
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $reset_token;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="user_id")
     */
    private $cart_id;

    public function __construct()
    {
        $this->cart_id = new ArrayCollection();
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCartId(): Collection
    {
        return $this->cart_id;
    }

    public function addCartId(Cart $cartId): self
    {
        if (!$this->cart_id->contains($cartId)) {
            $this->cart_id[] = $cartId;
            $cartId->setUserId($this);
        }

        return $this;
    }

    public function removeCartId(Cart $cartId): self
    {
        if ($this->cart_id->removeElement($cartId)) {
            // set the owning side to null (unless already changed)
            if ($cartId->getUserId() === $this) {
                $cartId->setUserId(null);
            }
        }

        return $this;
    }
}
