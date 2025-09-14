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
            'ticket_no' => $this->TICKETNO,
            'days_parked' => $this->days_parked,
            'hours_parked' => $this->hours_parked,
            'plate_no'  => $this->PLATENO,
            'qr_code'   => $this->QRCODE,
            'park_datetime' => $this->PARKDATETIME,
            'parkout_datetime' => $this->PARKOUTDATETIME,
            'remarks' => $this->REMARKS,
            'mode_of_payment' => $this->mode_of_payment,
            'park_fee' => $this->PARKFEE,
        ];
    }
}
