<?php

namespace App\Services\Avatars;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Enums\ImageDriver;
use Spatie\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Avatar
{
    public function __construct(private string $fileName) {}

    public static function upload(UploadedFile $uploadedFile): self
    {
        $fileName = Str::random(40).JPG_EXTENSION;
        $tmpFilePath = storage_path('app').'/'.$fileName;

        Image::useImageDriver(ImageDriver::Gd)
            ->loadFile($uploadedFile)
            ->fit(Fit::Crop, config('avatar.size.width'), config('avatar.size.height'))
            ->save($tmpFilePath);

        $filePath = Storage::putFileAs(config('avatar.storage_dir'), $tmpFilePath, $fileName);

        if (! $filePath) {
            throw new \RuntimeException('Unable to store avatar file.');
        }

        File::delete($tmpFilePath);

        return new self($fileName);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFilePath(): string
    {
        return config('avatar.storage_dir').'/'.$this->fileName;
    }

    public function getContent(bool $base64 = false): ?string
    {
        $content = Storage::get($this->getFilePath());

        return $base64 ? base64_encode($content) : $content;
    }

    public function delete(): bool
    {
        if (! $this->exists()) {
            return false;
        }

        $isDeleted = Storage::delete($this->getFilePath());

        if (! $isDeleted) {
            throw new \RuntimeException('Unable to delete avatar file.');
        }

        return true;
    }

    public function exists(): bool
    {
        return Storage::exists($this->getFilePath());
    }
}
