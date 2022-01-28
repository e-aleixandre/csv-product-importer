<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Uid\Ulid;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="ulid")
     * @ORM\CustomIdGenerator(class=UlidGenerator::class)
     */
    private Ulid $id;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Category $category;

    /**
     * @ORM\Column(type="float")
     */
    private float $price;

    /**
     * @ORM\Column(type="integer")
     */
    private int $stock;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $lastBoughtAt;

    /**
     * @ORM\OneToMany(targetEntity=ProductDetail::class, mappedBy="product", orphanRemoval=true, cascade={"persist"})
     */
    private $productDetails;

    public function __construct(float $price, int $stock, \DateTimeImmutable $lastBoughtAt, Category $category)
    {
        $this->id = new Ulid();
        $this->price = $price;
        $this->stock = $stock;
        $this->lastBoughtAt = $lastBoughtAt;
        $this->category = $category;
        $this->productDetails = new ArrayCollection();
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getLastBoughtAt(): \DateTimeImmutable
    {
        return $this->lastBoughtAt;
    }

    public function setLastBoughtAt(\DateTimeImmutable $lastBoughtAt): self
    {
        $this->lastBoughtAt = $lastBoughtAt;

        return $this;
    }

    public function addProductDetail(ProductDetail $productDetail): self
    {
        if (!$this->productDetails->contains($productDetail)) {
            $this->productDetails[] = $productDetail;
            $productDetail->setProduct($this);
        }

        return $this;
    }

    public function getProductDetails(): Collection
    {
        return $this->productDetails;
    }
}
