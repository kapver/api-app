<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class PhotoService
{
    public function upload(UploadedFile $photo): string
    {
        $fileName = time() . bin2hex(random_bytes(3)) . '.jpg';
        $filePath = 'images/users/' . $fileName;

        $tmpPath = $photo->path();

        $manager = new ImageManager(new Driver());
        $image = $manager->read($tmpPath)
            ->cover(70, 70)
            ->toJpeg(90);

        Storage::disk('public')->put($filePath, $image);

        unlink($tmpPath);

        return 'images/users/' . $fileName;
    }
}