<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\KommerceTemplates\Lib\SassServer;
use inklabs\KommerceTemplates\Lib\TwigThemeConfig;

class ScssController extends Controller
{
    public function serve($theme, $section, Request $request)
    {
        $formatter = $request->query('formatter', 'compressed');
        $cacheDir = __DIR__ . '/../../../storage/scss_cache';
        $rootScssDirectory = __DIR__ . '/../../../resources/assets/scss';

        $server = new SassServer(
            $rootScssDirectory,
            TwigThemeConfig::loadConfigFromTheme($theme, $section),
            $formatter,
            $cacheDir
        );

        $server->serve();

        exit;
    }
}
