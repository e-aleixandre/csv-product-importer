<?php

namespace App\Messenger\Commands;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Product;
use App\Entity\ProductDetail;
use App\Entity\ValueObject\Language;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ParseFileCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ParseFileCommand $message)
    {
        $file = $this->entityManager->find(File::class, $message->getFileId());

        if (null === $file)
        {
            throw new UnrecoverableMessageHandlingException();
        }

        $fileStream = fopen($file->getPath(), 'rb');

        if (!$fileStream)
        {
            throw new FileException();
        }

        // Discard csv heading
        fgets($fileStream);

        /** @var Category|null $category */
        $category = null;
        /** @var Product|null $product */
        $product = null;

        while (!feof($fileStream))
        {
            $line = explode(',', fgets($fileStream));

            if (count($line) < 6)
            {
                continue;
            }

            // If no description, it's a category
            if (empty($line[2]))
            {
                $category = $this->createCategoryIfDoesNotExist($line[1]);
                continue;
            }

            // ProductName and ProductDescription
            if (empty($line[0]))
            {
                $this->addProductDetails($line, $product);
                continue;
            }

            // new Product
            $product = $this->createProduct($line, $category);
        }

        fclose($fileStream);

        $file->setEnded(true);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        unlink($file->getPath());
    }

    private function createCategoryIfDoesNotExist(string $name): Category
    {
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $name]);

        if (null === $category)
        {
            $category = new Category($name);
            $this->entityManager->persist($category);
        }

        return $category;
    }

    private function createProduct(array $line, Category $category): Product
    {
        $product = new Product(
            (float)$line[3],
            (int)$line[4],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $line[5]),
            $category
        );

        $this->entityManager->persist($product);

        $this->addProductDetails($line, $product);

        return $product;
    }

    private function addProductDetails(array $line, Product $product): void
    {
        $language = new Language(
            preg_replace('/\s/', '', $line[6])
        );

        $productDetail = new ProductDetail($line[1], $line[2], $language);

        $product->addProductDetail($productDetail);

        $this->entityManager->persist($product);
    }

}