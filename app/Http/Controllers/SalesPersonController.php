<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesPerson;

class SalesPersonController extends Controller
{
    
   public function index(){

     $salesPerson =   SalesPerson::where('cancelled',0)->get();

     return Inertia('SalesPerson/Index',[
       'salesPerson' => $salesPerson
     ]);
   }
}
