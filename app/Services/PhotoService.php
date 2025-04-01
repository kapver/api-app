<?php

namespace App\Services;

use App\Models\User;

class PhotoService
{
    public function upload($photo): string
    {
        $fileName = time() . bin2hex(random_bytes(3)) . '.' . $photo->getClientOriginalExtension();
        $filePath = 'images/users/' . $fileName;
        $photo->move(public_path('images/users'), $fileName);
        $baseUrl = env('APP_URL');
        return $baseUrl . '/' . $filePath;
    }
}