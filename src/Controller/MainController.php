<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ValueObject\Language;
use App\Messenger\Commands\UploadCSVCommand;
use App\Messenger\Queries\FindProductsByCategoryAndLocaleQuery;
use App\Messenger\Queries\FindProductsByLocaleQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MainController extends AbstractController
{
    private ValidatorInterface $validator;
    private MessageBusInterface $bus;

    public function __construct(MessageBusInterface $bus, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->bus = $bus;
    }

    /**
     * @Route("/", methods={"POST"})
     */
    public function loadCSV(Request $request): JsonResponse
    {
        /** @var UploadedFile $inputFile */
        $inputFile = $request->files->get('inputFile');

        $violations = $this->validator->validate($inputFile, [
            new Assert\NotNull(),
            new Assert\NotBlank(),
            new Assert\File()
        ]);

        if ($violations->count()) {
            return $this->json([
                'success' => false,
                'error' => 'inputFile field must be a file'
            ]);
        }

        $this->bus->dispatch(new UploadCSVCommand($inputFile));

        return $this->json([]);
    }

    /**
     * @Route("{locale}/categories/{category}", methods={"GET"})
     */
    public function categories(string $locale, string $category): JsonResponse
    {
        $language = new Language($locale);

        $query = new FindProductsByCategoryAndLocaleQuery($category, $language);

        $envelope = $this->bus->dispatch($query);

        $handledStamp = $envelope->last(HandledStamp::class);

        return $this->json(
            $handledStamp->getResult()
        );
    }

    /**
     * @Route("{locale}/products", methods={"GET"})
     */
    public function products(string $locale): JsonResponse
    {
        $language = new Language($locale);

        $query = new FindProductsByLocaleQuery($language);

        $envelope = $this->bus->dispatch($query);

        $handledStamp = $envelope->last(HandledStamp::class);

        return $this->json(
            $handledStamp->getResult()
        );
    }
}
