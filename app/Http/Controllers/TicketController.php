<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

           return inertia('Parkin/Index',);
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
    // Validate inputs
    $data = $request->validate([
        'PLATENO' => 'required|string',
        'PARKYEAR' => 'nullable|integer',
        'PARKMONTH' => 'nullable|integer',
        'PARKDAY' => 'nullable|integer',
        'PARKHOUR' => 'nullable|integer',
        'PARKMINUTE' => 'nullable|integer',
        'PARKSECOND' => 'nullable|integer',
    ]);

    // Combine into a single datetime if all parts exist
    if (
        isset($data['PARKYEAR'], $data['PARKMONTH'], $data['PARKDAY'], 
              $data['PARKHOUR'], $data['PARKMINUTE'], $data['PARKSECOND'])
    ) {
        $data['PARKDATETIME'] = \Carbon\Carbon::create(
            $data['PARKYEAR'],
            $data['PARKMONTH'],
            $data['PARKDAY'],
            $data['PARKHOUR'],
            $data['PARKMINUTE'],
            $data['PARKSECOND']
        );
    } else {
        // fallback to now if parts are missing
        $data['PARKDATETIME'] = now();
    }

    $data['CANCELLED'] = 0;
     $data['TICKETNO'] = 0;
    // Use transaction to ensure atomicity
    DB::transaction(function () use ($data) {
        // Step 1: create the ticket (TICKETNO temporarily null)
        $ticket = Ticket::create($data);

        // Step 2: generate TICKETNO based on the inserted ID
        $ticketNo = (string) (1000000 + $ticket->id);

        // Step 3: update the ticket with TICKETNO
        $ticket->update(['TICKETNO' => $ticketNo]);
    });

    return back()->with('success', 'Ticket created successfully!');
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
