<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditConfigurationController extends Controller
{
    public function post(Request $request)
    {
        $key = $request->input('key');
        $value = $request->input('value');
        $isActive = (bool) $request->input('isActive', false);
        dd($key, $value, $isActive);
        // TODO: Save config value
    }
}
