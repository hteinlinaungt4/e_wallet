<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $title = '';
        if($this->type == 1){
           $title = 'From ' . $this->source->name;
        }else if($this->type == 2){
            $title = 'To ' . $this->source->name;
        }
        return [
            'trx_id' => $this->trx_id,
            'ref_no' => $this->ref_no,
            'amount' => $this->amount,
            'type' => $this->type,
            'date_time' => Carbon::parse($this->created_at)->format('Y-m-d H:m:s'),
            'source' => $title,
        ];
    }
}
