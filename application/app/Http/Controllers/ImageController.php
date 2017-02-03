<?php
namespace App\Http\Controllers;

class ImageController extends Controller
{
    public function get($imagePath)
    {
        $filePath = storage_path() . '/files/' . $imagePath;

        $this->serveFile($filePath);
    }
}
