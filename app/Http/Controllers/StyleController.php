<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use inklabs\KommerceTemplates\Lib\SassServer;

class StyleController extends Controller
{
    public function serve(Request $request)
    {
        $formatter = $request->query('formatter', 'compressed');
        $cacheDir = __DIR__ . '/../../../storage/scss_cache';
        $mainScssDirectory = __DIR__ . '/../../../resources/assets/scss';

        $server = new SassServer(
            $mainScssDirectory,
            env('BASE_TEMPLATE'),
            env('BOOTSWATCH_THEME'),
            $formatter,
            $cacheDir
        );

        $server->serve();

        exit;
    }
}
