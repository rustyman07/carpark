<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'ticket_no' => $this->ticket_no,
            'days_parked' => $this->days_parked,
            'hours_parked' => $this->hours_parked,
            'plate_no'  => $this->plate_no,
            'qr_code'   => $this->qr_code,
            'park_datetime' => $this->park_datetime,
            'park_out_datetime' => $this->park_out_datetime,
            'remarks' => $this->remarks,
            'mode_of_payment' => $this->mode_of_payment,
            'park_fee' => $this->park_fee,
        ];
    }
}
