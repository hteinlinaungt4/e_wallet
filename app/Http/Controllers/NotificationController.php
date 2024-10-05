<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    //

    public function index(){
        $user = Auth::guard('web')->user();
        $notifications = $user->notifications()->paginate(5);
        return view('frontend.notification',compact('notifications'));
    }

    public function show($id){
        $user = Auth::guard('web')->user();
        $notification = $user->notifications()->where('id',$id)->first();
        $notification->markAsRead();
        return view('frontend.notificationdetail',compact('notification'));
    }
}
