<?php

namespace App\Http\Controllers;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    
    public function index(){

        $company = Company::findOrFail(1);
        return Inertia('Company/Index',[
            'company' => $company
        ]);
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'id'                              => 'required|integer|exists:companies,id',
        'name'                            => 'required|string|max:255',
        'address'                         => 'required|string|max:255',
        'contact'                         => 'required|string|max:20',

        // Car billing
        'rate'                            => 'required|in:perhour,perday,combination',
        'rate_perhour'                    => 'nullable|numeric|min:0',
        'rate_perday'                     => 'nullable|numeric|min:0',
        'post_paid_rate'                  => 'nullable|numeric|min:0',
        'hourly_billing_limit'            => 'nullable|integer|min:1',
        'grace_minutes'                   => 'nullable|integer|min:0',
        'additional_hour_block'           => 'nullable|integer|min:1',
        'additional_rate_per_block'       => 'nullable|numeric|min:0',

        // Motor billing
        'motor_rate'                      => 'required|in:perhour,perday,combination',
        'motor_rate_perhour'              => 'nullable|numeric|min:0',
        'motor_rate_perday'               => 'nullable|numeric|min:0',
        'motor_hourly_billing_limit'      => 'nullable|integer|min:1',
        'motor_grace_minutes'             => 'nullable|integer|min:0',
        'motor_additional_hour_block'     => 'nullable|integer|min:1',
        'motor_additional_rate_per_block' => 'nullable|numeric|min:0',
    ]);

    $company = Company::findOrFail($id);

    // Clear car combination-only fields if not using combination rate
    if ($validated['rate'] !== 'combination') {
        $validated['hourly_billing_limit']      = null;
        $validated['additional_hour_block']     = null;
        $validated['additional_rate_per_block'] = null;
    }

    // Clear motor combination-only fields if not using combination rate
    if ($validated['motor_rate'] !== 'combination') {
        $validated['motor_hourly_billing_limit']      = null;
        $validated['motor_additional_hour_block']     = null;
        $validated['motor_additional_rate_per_block'] = null;
    }

    // Clear per-hour fields if not applicable
    if (!in_array($validated['rate'], ['perhour', 'combination'])) {
        $validated['rate_perhour'] = null;
    }
    if (!in_array($validated['motor_rate'], ['perhour', 'combination'])) {
        $validated['motor_rate_perhour'] = null;
    }

    // Clear per-day fields if not applicable
    if (!in_array($validated['rate'], ['perday', 'combination'])) {
        $validated['rate_perday'] = null;
    }
    if (!in_array($validated['motor_rate'], ['perday', 'combination'])) {
        $validated['motor_rate_perday'] = null;
    }

    $company->update($validated);

    return redirect()->back()->with('success', 'Company updated successfully!');
}


}
