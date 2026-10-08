<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'KasirApp Store'),
            'store_address' => Setting::get('store_address', ''),
            'store_phone' => Setting::get('store_phone', ''),
            'store_email' => Setting::get('store_email', ''),
            'tax_rate' => Setting::get('tax_rate', '0'),
            'receipt_footer' => Setting::get('receipt_footer', 'Terima kasih atas kunjungan Anda!'),
            'currency' => Setting::get('currency', 'Rp'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:100',
            'store_address' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:30',
            'store_email' => 'nullable|email|max:100',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'receipt_footer' => 'nullable|string|max:255',
            'currency' => 'required|string|max:10',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
