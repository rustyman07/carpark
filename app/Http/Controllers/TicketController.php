<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  
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

    public function showLogs(Request $request)
    {

        $type     = $request->input('type', 'PARK-IN'); // default
        $dateFrom = $request->input('dateFrom');
        $dateTo   = $request->input('dateTo');

        $query = Ticket::where('CANCELLED', 0)
            ->select('TICKETNO', 'PLATENO', 'PARKDATETIME', 'PARKOUTDATETIME')
            ->orderByDesc('created_at');

        $dateColumn = $type === 'PARK-IN' ? 'PARKDATETIME' : 'PARKOUTDATETIME';

        // Only apply filter if values are provided
        if ($dateFrom && $dateTo) {
            $query->whereDate($dateColumn, '>=', $dateFrom)
                ->whereDate($dateColumn, '<=', $dateTo);
        }

        return inertia('Logs/Index', [
            'Tickets' => $query->paginate(1),

        ]);

    }

    public function park_out(Request $request)
    {
        return inertia('Parkout/Index',);

    }


    public function submit_park_out(Request $request)
    {
        

         $data = $request->validate([
        'TICKETNO' => 'required|string',
        'PARKOUTYEAR' => 'nullable|integer',
        'PARKOUTMONTH' => 'nullable|integer',
        'PARKOUTDAY' => 'nullable|integer',
        'PARKOUTHOUR' => 'nullable|integer',
        'PARKOUTMINUTE' => 'nullable|integer',
        'PARKOUTSECOND' => 'nullable|integer',
    ]);

        if (
        isset($data['PARKOUTYEAR'], $data['PARKOUTMONTH'], $data['PARKOUTDAY'], 
              $data['PARKOUTHOUR'], $data['PARKOUTMINUTE'], $data['PARKOUTSECOND'])
    ) {
        $data['PARKOUTDATETIME'] = \Carbon\Carbon::create(
            $data['PARKOUTYEAR'],
            $data['PARKOUTMONTH'],
            $data['PARKOUTDAY'],
            $data['PARKOUTHOUR'],
            $data['PARKOUTMINUTE'],
            $data['PARKOUTSECOND']
        );
    } else {
        // fallback to now if parts are missing
        $data['PARKOUTDATETIME'] = now();
    }

$settings = Setting::find(1);
      
    // ✅ Find ticket by TICKETNO
    $ticket = Ticket::where('TICKETNO', $data['TICKETNO'])->first();

    if (!$ticket) {
        return response()->json(['error' => 'Ticket not found'], 404);
    }



    // Optionally, update the ticket with PARKOUTDATETIME
    $ticket->PARKOUTDATETIME = $data['PARKOUTDATETIME'];

    $parkDateTime = Carbon::parse($ticket->PARKDATETIME);
    $parkDateOutTime = Carbon::parse($ticket->PARKOUTDATETIME);


    $days = $parkDateTime->diffInDays($parkDateOutTime) + 1;

    $ticket->PARKFEE = $days *  (float)$settings->FEE;


    $ticket->save();

//   return inertia('Parkout/Index', [
//             'ticket' => $ticket,
//              'success' => true,

//         ]);
//echo $ticket->PARKFEE;
return inertia('Parkout/Index', [
    'ticket' => $ticket,
    'success' => true,
]);
    }


}