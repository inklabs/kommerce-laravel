<?php
namespace App\Http\Controllers;

class AssetController extends Controller
{
    public function serve($theme, $path)
    {
        $assetLocationService = $this->getAssetLocationService();
        $filePath = $assetLocationService->getAssetFilePathByTheme($theme, $path);

        $this->serveFile($filePath);
    }
}
