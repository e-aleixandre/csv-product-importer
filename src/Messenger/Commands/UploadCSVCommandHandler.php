<?php

namespace App\Messenger\Commands;

use App\Entity\File;
use App\Messenger\Commands\ParseFileCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UploadCSVCommandHandler implements MessageHandlerInterface
{
    private MessageBusInterface $bus;
    private ParameterBagInterface $parameterBag;
    private EntityManagerInterface $entityManager;

    public function __construct(
        MessageBusInterface $bus,
        ParameterBagInterface $parameterBag,
        EntityManagerInterface $entityManager
    )
    {
        $this->bus = $bus;
        $this->parameterBag = $parameterBag;
        $this->entityManager = $entityManager;
    }

    public function __invoke(UploadCSVCommand $command)
    {
        $newFile = $command->file()->move(
            $this->parameterBag->get('uploads_directory'),
            $command->file()->getFilename()
        );

        $file = new File();

        $file->setPath($newFile->getPathname());

        $this->entityManager->persist($file);
        $this->entityManager->flush();

        $this->bus->dispatch(new ParseFileCommand($file->getId()));
    }
}