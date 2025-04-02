<?php

namespace App\Services;

use App\Jobs\OptimizePhotoJob;
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

        $width = (int) config('app.tinify_crop_width');
        $height = (int) config('app.tinify_crop_height');

        // Consider to use one library to crop
        // Used to avoid big transfer size on photo optimization
        $manager = new ImageManager(new Driver());
        $image = $manager->read($tmpPath)
            ->cover($width, $height)
            ->toJpeg(90);

        // Save cropped unoptimized
        Storage::disk('public')->put($filePath, $image);

        // Queue optimization
        OptimizePhotoJob::dispatchSync(Storage::disk('public')->path($filePath));

        // Delete tmp original file
        unlink($tmpPath);

        return 'images/users/' . $fileName;
    }

    public function optimize(string $path)
    {
        $width = (int) config('app.tinify_crop_width');
        $height = (int) config('app.tinify_crop_height');

        \Tinify\setKey(config('app.tinify_api_key'));

        $source = \Tinify\fromFile($path);
        $resized = $source->resize(array(
            'method' => 'thumb',
            'width' => $width,
            'height' => $height,
        ));

        $resized->toFile($path);
    }
}