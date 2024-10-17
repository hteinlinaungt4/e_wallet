<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $unreadnoti = 0;


        if(auth()->guard('api')->check()){
            $unreadnoti = auth()->guard('api')->user()->unreadNotifications()->count();
        }

        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'account_number' => $this->wallet ? $this->wallet->account_number : '_',
            'balance' => $this->wallet ? number_format($this->wallet->amount) : '_',
            'profile' => 'https://ui-avatars.com/api/?size=110&background=3d4ad4&color=fff&name='.$this->name,
            'noticount' => $unreadnoti,
            'qr_code_value' => $this->phone,
        ];
    }
}
