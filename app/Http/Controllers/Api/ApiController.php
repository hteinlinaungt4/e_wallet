<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ProfileResource;
use App\Notifications\GeneralNotification;
use App\Http\Requests\TransferFormValidate;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\NotificationResource;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\NotificationDetailResource;

class ApiController extends Controller
{
    public function profile(){
       $user = Auth::user();

       $data = new ProfileResource($user);

       return success('message',$data);
    }

    public function transaction(Request $request){

        $user=Auth::user();
        $transactions=Transaction::with('user','source')->where('user_id',$user->id)->orderBy('created_at','desc');

        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }

        // Apply date filter if provided
        if ($request->date) {
            $transactions = $transactions->whereDate('created_at', $request->date);
        }

        // Paginate the results
        $transactions = $transactions->paginate(5);


        $data = TransactionResource::collection($transactions)->additional(['result => 1','message' => 'success']);

        return $data;
    }


    public function transactionDetail($trxId){
        $user = Auth::user();
        $transaction=Transaction::where('user_id',$user->id)->where('trx_id',$trxId)->firstorFail();

        $transactionDetail = new TransactionResource($transaction);

        return success('success',$transactionDetail);
    }


    public function notifications(){
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(1);

        $data =NotificationResource::collection($notifications)->additional(['result' => 1, 'message' => 'success']);
        return $data;
    }

    public function notificationdetail($id){
        $user = Auth::user();
        $notification = $user->notifications()->where('id',$id)->firstorFail();
        $notification->markAsRead();

        $data = new NotificationDetailResource($notification);


        return success('success',$data);

    }

    public function account_vertify(Request $request){
        $user = Auth::user();
        if($request->phone){
            if($user->phone != $request->phone){
                $user=User::where('phone',$request->phone)->first();
                if($user){
                    return success('success',[
                        'name' => $user->name,
                        'phone' => $user->phone,
                    ]);
                }
            }
            return fail("You can't transfer money to yourself!",null);
        }

        return fail('Phone Number Invalid',null);
    }

    public function account_confirm(TransferFormValidate $request){

        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');


        if($hash_value1 !== $request->hash_value){
            return fail('Something is Wrong!',null);
        }

        $user = Auth::user();
        if ($user->phone == $request->to_phone) {
            return fail("You can't transfer money to yourself!",null);
        }
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return fail('The recieve phone number is invalid!',null);
        }

        if ($user->wallet->amount < $request->amount) {
            return fail('Balance Enough!',null);
        }

        if (!$user->wallet || !$check_tophone->wallet) {
            return fail('Something Wrong!',null);
        }

        return success('success',[
            'to_user' => $check_tophone->name,
            'to_phone' => $request->to_phone,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

    }


    public function account_complete(Request $request){
        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return fail('Something is Wrong!',null);
        }

        $user = Auth::user();
        $check_tophone = User::where('phone', $request->to_phone)->first();

        if ($user->phone == $request->to_phone) {
            return fail("You can't transfer money to yourself!",null);
        }

        if (!$check_tophone) {
            return fail('The recieve phone number is invalid!',null);
        }

        if ($user->wallet->amount < $request->amount) {
            return fail('Balance Enough!',null);
        }


        $oldpassword = $user->password;
        if (!Hash::check($request->password, $oldpassword)) {
           return fail('The password is invalid!',null);
        }

        if(!$request->password){
            return fail('The password field is required!',null);
        }


        $from_account = $user;
        $to_account = $check_tophone;
        $amount = $request->amount;
        $description = $request->description;


        if (!$from_account->wallet || !$to_account->wallet) {
            return fail('Something Wrong!',null);
        }


        DB::beginTransaction();
        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();
            $from_account_transactions= new Transaction();
            $from_account_transactions->ref_no=$ref_no;
            $from_account_transactions->trx_id=UUIDGenerate::trxId();
            $from_account_transactions->user_id=$from_account->id;
            $from_account_transactions->type=2;
            $from_account_transactions->amount=$amount;
            $from_account_transactions->source_id=$to_account->id;
            $from_account_transactions->description=$description;
            $from_account_transactions->save();

            $to_account_transactions= new Transaction();
            $to_account_transactions->ref_no=$ref_no;
            $to_account_transactions->trx_id=UUIDGenerate::trxId();
            $to_account_transactions->user_id=$to_account->id;
            $to_account_transactions->type=1;
            $to_account_transactions->amount=$amount;
            $to_account_transactions->source_id=$from_account->id;
            $to_account_transactions->description=$description;
            $to_account_transactions->save();



            // send noti for from_account
            $title= 'E_Money Transfered!';
            $message = 'Your e_money transfered ' . $amount . ' MMK to ' . $to_account->name .'('.$to_account->phone.')';
            $sourceable_id = $from_account_transactions->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transactions->trx_id);
            Notification::send($from_account, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));

            // // send noti for to_account
            $title= 'E_Money Received!';
            $message = 'Your e_money received ' . $amount . ' MMK to ' . $from_account->name .'('.$from_account->phone.')';
            $sourceable_id = $from_account_transactions->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$to_account_transactions->trx_id);
            Notification::send($to_account, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));


            DB::commit();

            return success('You have been transfer money Successfully!',[
                'trx_id' =>$from_account_transactions->trx_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return fail('Something went wrong','null');
        }
    }


    public function transferScan(Request $request){

        $user = Auth::user();

        $from_account = Auth::user()->phone;
        $to_phone=User::where('phone',$request->to_phone)->select('phone','name')->first();
        if(!$to_phone){
            return fail('Qr code is invalid',null);
        }
        if($from_account == $to_phone->phone){
            return fail('You cannot transfer yourself!',null);
        }

        return success('success',[
            'from_phone' => $user->phone,
            'from_user' => $user->name,
            'to_phone' => $to_phone->phone,
            'to_user' => $to_phone->name,
        ]);
    }

    public function transferComfirmScan(TransferFormValidate $request){

        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return fail('Something is Wrong!',null);
        }


        $user = Auth::user();
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return fail('The recieve phone number is invalid!',null);
        }
        if ($user->phone == $request->to_phone) {
            return fail('You can\'t transfer money to yourself!',null);
        }

        if ($user->wallet->amount < $request->amount) {
            return fail('Balance Enough!',null);
        }

        if (!$user->wallet || !$check_tophone->wallet) {
            return fail('Something Wrong!',null);
        }

        $hash_value=$request->hash_value;
        $to_user = $check_tophone->name;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;

        return success('success',[
            'from_phone' => $user->phone,
            'from_user' => $user->name,
            'to_phone' => $to_phone,
            'to_user' => $to_phone,
            'amount' => $amount,
            'description' => $description,
            'hash_value' => $hash_value1,
        ]);
    }


    public function transferCompleteScan(Request $request){
        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return fail('Something is Wrong!',null);
        }

        $user = Auth::user();
        $check_tophone = User::where('phone', $request->to_phone)->first();

        if ($user->phone == $request->to_phone) {
            return fail("You can't transfer money to yourself!",null);
        }

        if (!$check_tophone) {
            return fail('The recieve phone number is invalid!',null);
        }

        if ($user->wallet->amount < $request->amount) {
            return fail('Balance Enough!',null);
        }


        $oldpassword = $user->password;
        if (!Hash::check($request->password, $oldpassword)) {
           return fail('The password is invalid!',null);
        }

        if(!$request->password){
            return fail('The password field is required!',null);
        }


        $from_account = $user;
        $to_account = $check_tophone;
        $amount = $request->amount;
        $description = $request->description;


        if (!$from_account->wallet || !$to_account->wallet) {
            return fail('Something Wrong!',null);
        }


        DB::beginTransaction();
        try {
            $from_account_wallet = $from_account->wallet;
            $from_account_wallet->decrement('amount', $amount);
            $from_account_wallet->update();

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();
            $from_account_transactions= new Transaction();
            $from_account_transactions->ref_no=$ref_no;
            $from_account_transactions->trx_id=UUIDGenerate::trxId();
            $from_account_transactions->user_id=$from_account->id;
            $from_account_transactions->type=2;
            $from_account_transactions->amount=$amount;
            $from_account_transactions->source_id=$to_account->id;
            $from_account_transactions->description=$description;
            $from_account_transactions->save();

            $to_account_transactions= new Transaction();
            $to_account_transactions->ref_no=$ref_no;
            $to_account_transactions->trx_id=UUIDGenerate::trxId();
            $to_account_transactions->user_id=$to_account->id;
            $to_account_transactions->type=1;
            $to_account_transactions->amount=$amount;
            $to_account_transactions->source_id=$from_account->id;
            $to_account_transactions->description=$description;
            $to_account_transactions->save();



            // send noti for from_account
            $title= 'E_Money Transfered!';
            $message = 'Your e_money transfered ' . $amount . ' MMK to ' . $to_account->name .'('.$to_account->phone.')';
            $sourceable_id = $from_account_transactions->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$from_account_transactions->trx_id);
            Notification::send($from_account, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));

            // // send noti for to_account
            $title= 'E_Money Received!';
            $message = 'Your e_money received ' . $amount . ' MMK to ' . $from_account->name .'('.$from_account->phone.')';
            $sourceable_id = $from_account_transactions->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$to_account_transactions->trx_id);
            Notification::send($to_account, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));


            DB::commit();

            return success('You have been transfer money Successfully!',[
                'trx_id' =>$from_account_transactions->trx_id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return fail('Something went wrong','null');
        }
    }



}
