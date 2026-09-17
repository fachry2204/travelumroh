<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('setting_value', 'setting_key');
        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->except(['_method', 'logo', 'favicon']);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $value, 'setting_group' => $this->determineGroup($key)]
            );
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            Setting::updateOrCreate(
                ['setting_key' => 'app_logo'],
                ['setting_value' => $path, 'setting_group' => 'general']
            );
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('settings', 'public');
            Setting::updateOrCreate(
                ['setting_key' => 'app_favicon'],
                ['setting_value' => $path, 'setting_group' => 'general']
            );
        }

        return response()->json(['success' => true, 'message' => 'Pengaturan berhasil disimpan']);
    }

    private function determineGroup($key)
    {
        if (str_starts_with($key, 'mpwa_') || str_starts_with($key, 'duitku_')) {
            return 'api';
        }
        if (str_starts_with($key, 'mail_')) {
            return 'mail';
        }
        return 'general';
    }
}
