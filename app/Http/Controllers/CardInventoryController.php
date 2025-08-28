<?php

namespace App\Http\Controllers;
use App\Models\CardInventory;
use App\Models\CardTemplate;

use Illuminate\Http\Request;

class CardInventoryController extends Controller
{
    //
     public function index()
    {

        $cardTemplate =  CardTemplate::where('CANCELLED',0)->get();
        $cardInventory = CardInventory::orderBy('id','asc')->get();


           return inertia('CardInventory/Index',[
            'cardTemplate' => $cardTemplate
           ]);
    }
}
