<?php

namespace App\Controller;

use App\Messenger\Commands\UploadCSVCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function loadCSV(Request $request): Response
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
}
