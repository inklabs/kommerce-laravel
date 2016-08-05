<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use inklabs\KommerceTemplates\Lib\AssetLocationService;
use inklabs\KommerceTemplates\Lib\SassServer;

class AssetController extends Controller
{
    public function serve(Request $request, $theme, $path)
    {
        $assetLocationService = $this->getAssetLocationService();
        $filePath = $assetLocationService->getAssetFilePathByTheme($theme, $path);

        if (! file_exists($filePath)) {
            abort(404);
        }

        header('Content-Length: ' . filesize($filePath));
        header('Content-Type: ' . mime_content_type($filePath));
        header('Expires: ' . date('r', strtotime('now +1 week')));
        header('Last-Modified: ' . date('r', filemtime($filePath)));
        header('Cache-Control: max-age=604800');
        ob_clean();
        flush();

        readfile($filePath);
        exit;
    }
}
