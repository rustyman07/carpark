<?php

namespace App\Http\Controllers;
use App\Models\CardTemplate;
use Illuminate\Http\Request;

class CardTemplateController extends Controller
{
    //

    public function index()
    {
        $cardTemplate = CardTemplate::orderBy('id','asc')->get();
           return inertia('CardTemplate/Index',[
            'templates' =>  $cardTemplate
           ],);
    }

    public function store(Request $request )
    {
    
        $validated = $request->validate([
            'CARDNAME' => 'required|string|max:255',
            'NOOFDAYS' => 'required|integer|min:0',
            'PRICE'    => 'required|numeric|min:0',
            'DISCOUNT' => 'nullable|numeric|min:0',
            'AMOUNT'   => 'required|numeric|min:0',
        ]);

        CardTemplate::create($validated);

        return redirect()->route('cardTemplate.index')->with(
            'success','created succesfully');

    }


    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'CARDNAME' => 'required|string|max:255',
        'NOOFDAYS' => 'required|integer|min:0',
        'PRICE'    => 'required|numeric|min:0',
        'DISCOUNT' => 'nullable|numeric|min:0',
        'AMOUNT'   => 'required|numeric|min:0',
    ]);

    // 🔹 Find the record or throw 404 if not found
    $cardTemplate = CardTemplate::findOrFail($id);
    // 🔹 Update with validated data
    $cardTemplate->update($validated);

    return redirect()->route('cardTemplate.index')
        ->with('success', 'updated successfully');
}

}
