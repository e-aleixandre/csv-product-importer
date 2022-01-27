<?php

namespace App\Messenger\Commands;

use App\Entity\Category;
use App\Entity\File;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ParseFileMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private array $categories = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(ParseFileMessage $message)
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

        while (!feof($fileStream))
        {
            $line = explode(',', fgets($fileStream));

            if (empty($line[0]))
            {
                $this->createCategory($line[1], $file);

                continue;
            }

            $this->createProduct($line);
        }

        fclose($fileStream);

        $file->setEnded(true);
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        unlink($file->getPath());
    }

    private function createCategory(string $name, File $file): void
    {
        $category = new Category($name);
        $category->setFile($file);

        $this->entityManager->persist($category);

        $this->categories[$name] = $category;
    }

    private function createProduct(array $line): void
    {
        $date = preg_replace('/\s/', '', $line[5]);

        $product = new Product(
            $line[1],
            $line[2],
            (float)$line[3],
            (int)$line[4],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date),
            $this->categories[$line[0]]
        );

        $product->setCategory($this->categories[$line[0]]);

        $this->entityManager->persist($product);
    }
}