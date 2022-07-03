<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
/**
 * @ORM\Table(name="produits")
 * @Vich\Uploadable
 */
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    #[ORM\Column(type: 'string', length: 255)]
    private $description;

    #[ORM\Column(type: 'integer')]
    private $prix;

    #[ORM\Column(type: 'integer')]
    private $quantite;

     /**
     * @ORM\Column(type="datetime_immutable", options={"default":"CURRENT_TIMESTAMP"} )
     */
    private $createdAt;

     /**
     * @ORM\Column(type="datetime_immutable", options={"default":"CURRENT_TIMESTAMP"} )
     */
    private $updatedAt;

    /**
     * @Vich\UploadableField(mapping="products_image", fileNameProperty="imageName")
     * @Assert\Image(maxSize="8M")
     * @var File|null
     */
    private $imageFile;


    #[ORM\Column(type: 'string', length: 255)]
    private $imageName;


    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private $userproduit;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Order::class)]
    private $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestramps()
    {
        if($this->getCreatedAt()===null){
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    /***
     * @param File|Symfony\Component\HttpFoundation\File\uploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->setUpdatedAt(new \DateTimeImmutable);
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }


    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getUserproduit(): ?Users
    {
        return $this->userproduit;
    }

    public function setUserproduit(?Users $userproduit): self
    {
        $this->userproduit = $userproduit;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setProduit($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getProduit() === $this) {
                $order->setProduit(null);
            }
        }

        return $this;
    }

    

}
