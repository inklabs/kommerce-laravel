<?php
namespace App\Http\Controllers;

class AssetController extends Controller
{
    public function serve($theme, $section, $path)
    {
        $assetLocationService = $this->getAssetLocationService();
        $filePath = $assetLocationService->getAssetFilePathByTheme($theme, $section, $path);

        $this->serveFile($filePath);
    }
}
