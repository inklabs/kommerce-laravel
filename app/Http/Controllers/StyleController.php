<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\KommerceTemplates\Lib\SassServer;

class StyleController extends Controller
{
    public function serve(Request $request)
    {
        $formatter = $request->query('formatter', 'compressed');
        $cacheDir = __DIR__ . '/../../../storage/scss_cache';
        $rootScssDirectory = __DIR__ . '/../../../resources/assets/scss';

        $server = new SassServer(
            $rootScssDirectory,
            $this->getThemeConfig(),
            $formatter,
            $cacheDir
        );

        $server->serve();

        exit;
    }
}
