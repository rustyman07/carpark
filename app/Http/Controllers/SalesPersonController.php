<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesPerson;
use Inertia\Inertia;

class SalesPersonController extends Controller
{
    public function index(Request $request)
    {
        $salesPerson = SalesPerson::orderBy('lastname')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('SalesPerson/Index', [
            'salesPerson' => $salesPerson
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'middlename'=> 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'contact'   => 'nullable|string|max:20',
        ]);

        SalesPerson::create($request->only([
            'firstname', 'lastname', 'middlename', 'address', 'contact'
        ]));

        return redirect()->route('sales-person.index')->with('success', 'Sales Person added successfully.');
    }

    public function update(Request $request, $id)
    {
        $salesPerson = SalesPerson::findOrFail($id);

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'middlename'=> 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'contact'   => 'nullable|string|max:20',
        ]);

        $salesPerson->update($request->only([
            'firstname', 'lastname', 'middlename', 'address', 'contact'
        ]));

        return redirect()->route('sales-person.index')->with('success', 'Sales Person updated successfully.');
    }

    public function destroy($id)
    {
        // $salesPerson = SalesPerson::findOrFail($id);
        // $salesPerson->delete();

        // return redirect()->route('sales-person.index')->with('success', 'Sales Person deleted successfully.');
    }
}
