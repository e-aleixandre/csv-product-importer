<?php

namespace App\Messenger\Commands;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Product;
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

        fgets($fileStream);

        $category = null;

        while (!feof($fileStream))
        {
            $line = explode(',', fgets($fileStream));

            if (empty($line[0]))
            {
                $category = $this->createCategoryIfDoesNotExist($line[1]);

                continue;
            }

            $this->createProduct($line, $category);
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

    private function createProduct(array $line, Category $category): void
    {
        $date = preg_replace('/\s/', '', $line[5]);

        $product = new Product(
            $line[1],
            $line[2],
            (float)$line[3],
            (int)$line[4],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date),
            $category
        );

        $this->entityManager->persist($product);
    }
}