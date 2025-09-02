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
}
