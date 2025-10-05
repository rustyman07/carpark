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
        'id'                   => 'required|integer|exists:companies,id',
        'name'                 => 'required|string|max:255',
        'address'              => 'required|string|max:255',
        'contact'              => 'required|string|max:20',
        'rate'                 => 'required|in:perhour,perday,combination',
        'rate_perhour'         => 'nullable|numeric|min:0',
        'rate_perday'          => 'nullable|numeric|min:0',
        'post_paid_rate'       => 'nullable|numeric|min:0',
        'hourly_billing_limit' => 'nullable|integer|min:1',  
        'grace_minutes'        => 'nullable|integer|min:0',  
    ]);

    $company = Company::findOrFail($id);

    // If rate type is NOT "combination", clear those fields to avoid confusion
    if ($validated['rate'] !== 'combination') {
        $validated['hourly_billing_limit'] = null;
        $validated['grace_minutes'] = null;
    }

    $company->update($validated);

    return redirect()->back()->with('success', 'Company updated successfully!');
}




}
