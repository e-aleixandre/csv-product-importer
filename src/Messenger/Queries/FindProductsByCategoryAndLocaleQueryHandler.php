<?php

namespace App\Messenger\Queries;

use App\Entity\Product;
use App\Entity\ProductDetail;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindProductsByCategoryAndLocaleQueryHandler implements MessageHandlerInterface
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;

    public function __construct(ProductRepository $productRepository, CategoryRepository $categoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function __invoke(FindProductsByCategoryAndLocaleQuery $query): array
    {
        $category = $this->categoryRepository->findOneBy(['name' => $query->category()]);

        if (null === $category)
        {
            throw new EntityNotFoundException();
        }

        /** @var Product[] $products */
        $products = $this->productRepository->byCategoryIdAndLocale($category->getId(), $query->locale()->value());

        // Replace with a ViewFactory
        $productsArray = [];

        foreach($products as $product)
        {
            $productsArray[] = [
                'id' => $product['id']->toBase58(),
                'category' => $product['category']['name'],
                'name' => $product['productDetails'][0]['name'],
                'description' => $product['productDetails'][0]['description'],
                'locale' => $product['productDetails'][0]['language.value'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'lastBoughtAt' => $product['lastBoughtAt']->format('Y-m-d H:i')
            ];
        }

        return $productsArray;
    }
}