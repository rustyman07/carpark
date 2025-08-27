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
            'CARDNAME' => 'required|string|max:255',
            'NOOFDAYS' => 'required|integer|min:0',
            'PRICE'    => 'required|numeric|min:0',
            'DISCOUNT' => 'nullable|numeric|min:0',
           
        ]);

      $validated['AMOUNT'] = $validated['PRICE'] - $validated['DISCOUNT'];

        CardTemplate::create($validated);

        // return redirect()->route('cardTemplate.index')->with(
        //     'success','created succesfully');

//         return Inertia::render('CardTemplate/Index', [
//     'templates' => CardTemplate::orderBy('id','asc')->get(),
// ])->with('success', 'Template created successfully');

        return redirect()->back();

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
    public function update(Request $request, cardTemplate $cardTemplate)
    {
      $validated = $request->validate([
        'CARDNAME' => 'required|string|max:255',
         'NOOFDAYS' => 'required|integer|min:1',
        'PRICE'    => 'required|numeric|min:0',
        'DISCOUNT' => 'nullable|numeric|min:0',
     
    ]);

      $validated['AMOUNT'] = $validated['PRICE'] - $validated['DISCOUNT'];


    $cardTemplate->update($validated);

      return redirect()->back();

    // return redirect()->route('cardTemplate.index')
    //     ->with('success', 'updated successfully');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
