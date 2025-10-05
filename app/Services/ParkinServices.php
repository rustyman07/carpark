<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ParkInService
{
    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $parkDatetime = isset($data['park_year'])
                ? Carbon::create(
                    $data['park_year'],
                    $data['park_month'],
                    $data['park_day'],
                    $data['park_hour'] ?? 0,
                    $data['park_minute'] ?? 0,
                    $data['park_second'] ?? 0
                )
                : now();

            $data = array_merge($data, [
                'park_datetime' => $parkDatetime,
                'uuid' => Str::uuid(),
                'create_by' => Auth::id(),
                'park_in_by' => Auth::id(),
            ]);

            $ticket = Ticket::create($data);

            $ticket_no = '1' . sprintf('%06d', $ticket->id);
            $ticket->update([
                'ticket_no' => $ticket_no,
                'qr_code'   => bcrypt($ticket_no),
                'remarks'   => 'Unpaid',
            ]);

            Cache::forget('dashboard.metrics');
            Cache::forget('dashboard.latestParkin');

            return $ticket;
        });
    }
}






