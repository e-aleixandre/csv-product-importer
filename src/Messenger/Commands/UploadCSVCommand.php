<?php

namespace App\Messenger\Commands;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadCSVCommand
{
    private UploadedFile $file;

    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    public function file(): UploadedFile
    {
        return $this->file;
    }
}