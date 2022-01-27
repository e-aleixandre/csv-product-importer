<?php

namespace App\Messenger\Commands;

use Symfony\Component\Uid\Ulid;

class ParseFileCommand
{
    private Ulid $fileId;

    public function __construct(Ulid $fileId)
    {
        $this->fileId = $fileId;
    }

    public function getFileId(): Ulid
    {
        return $this->fileId;
    }
}