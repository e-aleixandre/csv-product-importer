<?php

namespace App\Messenger\Queries;

use App\Entity\Product;
use App\Entity\ProductDetail;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FindProductsByLocaleQueryHandler implements MessageHandlerInterface
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function __invoke(FindProductsByLocaleQuery $query): array
    {
        /** @var Product[] $products */
        $products = $this->productRepository->byLocale($query->locale()->value());

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