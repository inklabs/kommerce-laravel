<?php
namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Configuration\UpdateConfigurationCommand;
use inklabs\kommerce\Exception\KommerceException;

class EditConfigurationController extends Controller
{
    public function post(Request $request)
    {
        $configurations = $request->input('configuration');

        $totalUpdated = 0;
        foreach ($configurations as $configuration) {
            $key = Arr::get($configuration, 'key');
            $value = Arr::get($configuration, 'value');

            try {
                $this->dispatch(new UpdateConfigurationCommand(
                    $key,
                    $value
                ));

                $totalUpdated++;
            } catch (KommerceException $e) {
                $this->flashError('Failure updating configuration');
                return redirect()->route('admin.settings.store');
            }
        }

        if ($totalUpdated > 1) {
            $this->flashSuccess('Success updating ' . $totalUpdated . ' configurations');
        } else {
            $this->flashSuccess('Success updating configuration');
        }

        return redirect()->route('admin.settings.store');
    }
}
