<?php

namespace App\Http\Controllers;

use App\Models\CardTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $cardTemplate = CardTemplate::orderBy('id','asc')->get();
           return inertia('CardTemplate/Index',[
            'templates' =>  $cardTemplate
           ],);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $validated = $request->validate([
            'card_name'  => 'required|string|max:255',
            'no_of_days' => 'required|integer|min:1',
            'price'      => 'required|numeric|min:0',
            'discount'   => 'nullable|numeric|min:0',
        ]);

        // Compute amount
        $validated['amount'] = $validated['price'] - ($validated['discount'] ?? 0);

        CardTemplate::create($validated);

        return redirect()
            ->route('card-template.index')
            ->with('success', 'Template created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CardTemplate $cardTemplate)
    {
        $validated = $request->validate([
            'card_name'  => 'required|string|max:255',
            'no_of_days' => 'required|integer|min:1',
            'price'      => 'required|numeric|min:0',
            'discount'   => 'nullable|numeric|min:0',
        ]);

        $validated['amount'] = $validated['price'] - ($validated['discount'] ?? 0);

        $cardTemplate->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Template updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
