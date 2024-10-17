<?php

namespace App\Http\Controllers\Frontend;

use PDF;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\GeneralNotification;
use App\Http\Requests\TransferFormValidate;
use Illuminate\Support\Facades\Notification;


class PageController extends Controller
{
    public function home()
    {
        $user = Auth::guard('web')->user();
        return view('frontend.home', compact('user'));
    }

    // transfer
    public function transfer()
    {
        $user = Auth::guard('web')->user();
        return view('frontend.transfer', compact('user'));
    }

    // transfer comfirm
    public function transferComfirm(TransferFormValidate $request)
    {

        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return back()->withErrors(['to_phone' => 'Something is Wrong!']);
        }


        $user = Auth::guard('web')->user();
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return back()->withErrors(['to_phone' => 'The recieve phone number is invalid!']);
        }
        if ($user->phone == $request->to_phone) {
            return back()->withErrors(['to_phone' => "You can't transfer money to yourself!"]);
        }

        if ($user->wallet->amount < $request->amount) {
            return back()->withErrors(['to_phone' => "Balance Enough!"]);
        }

        if (!$user->wallet || !$check_tophone->wallet) {
            return back()->withErrors(['to_phone' => "Something Wrong!"]);
        }

        $hash_value=$request->hash_value;
        $to_user = $check_tophone->name;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        return view('frontend.transfercomfirm', compact('user', 'to_phone', 'amount', 'description', 'to_user','hash_value'));
    }

    // with ajax check verify_account
    public function verify_account(Request $request)
    {
        $authenticatedUserPhone = Auth::guard('web')->user()->phone;

        if ($authenticatedUserPhone !== $request->to_phone) {
            $user = User::where('phone', $request->to_phone)->first();

            if ($user) {
                $data = [
                    'status' => 'success',
                    'message' => $user->name, // Return the name of the user
                ];
                return response()->json($data, 200);
            } else {
                $data = [
                    'status' => 'fail',
                    'message' => 'The recieve phone number does not exist!',
                ];
                return response()->json($data, 200); // Return a 404 status code for not found
            }
        }

        $data = [
            'status' => 'fail',
            'message' => "You can't transfer money to yourself!",
        ];
        return response()->json($data, 200); // Return a 400 status code for a bad request
    }

    // password check
    public function passwordCheck(Request $request)
    {
        $user = Auth::guard('web')->user();
        $oldpassword = $user->password;
        if (Hash::check($request->password, $oldpassword)) {
            $data = [
                'status' => 'success',
            ];
            return response()->json($data, 200);
        }
        $data = [
            'status' => 'fail',
        ];
        return response()->json($data, 200);
    }

    // transfer complete
    public function transfer_complete(Request $request)
    {
        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return back()->withErrors(['to_phone' => 'Something is Wrong!']);
        }

        $user = Auth::guard('web')->user();
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return back()->withErrors(['to_phone' => 'The recieve phone number is invalid!']);
        }
        if ($user->phone == $request->to_phone) {
            return redirect()->route('user.transfer')->withErrors(['to_phone' => "You can't transfer money to yourself!"]);
        }
        $from_account = $user;
        $to_account = $check_tophone;
        $amount = $request->amount;
        $description = $request->description;


        if (!$from_account->wallet || !$to_account->wallet) {
            return back()->withErrors(['to_phone' => "Something Wrong!"]);
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
            return redirect()->route('user.home')->with(['success' => "You have been transfer money Successfully!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Something went wrong']);
        }
    }


    // transaction
    public function transaction(Request $request){
        $user = Auth::guard('web')->user();
        $transactions = Transaction::with('user', 'source')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');

        // Check if the 'type' is not 'all'
        if ($request->type) {
            $transactions = $transactions->where('type', $request->type);
        }

        // Apply date filter if provided
        if ($request->date) {
            $transactions = $transactions->whereDate('created_at', $request->date);
        }

        // Paginate the results
        $transactions = $transactions->paginate(5);



        // Return the view with the transactions data
        return view('frontend.transaction', compact('transactions'));
    }


    // pdf generate
    public function pdf(Request $request){

        $user = Auth::guard('web')->user();
        $transactions=Transaction::with('user','source')->where('user_id',$user->id)->orderBy('created_at','desc');
        if($request->type){
            $transactions=$transactions->where('type',$request->type);
        }
        if($request->date){
            $transactions=$transactions->whereDate('created_at',$request->date);
        }
        $transactions= $transactions->get();
        if ($transactions->isEmpty()) {
            return redirect()->back()->with('message', 'No transactions found for the selected filters.');
        }

        $file = PDF::loadView('frontend.pdfgenerate.transaction',compact('transactions'));
        return $file->download('transactions.pdf');
    }

    // transactionDetail

    public function transactionDetail($trx_id){
        $user = Auth::guard('web')->user();
        $transaction=Transaction::where('user_id',$user->id)->where('trx_id',$trx_id)->first();
        return view('frontend.transactiondetail',compact('transaction'));
    }

    // hash
    public function hashtransaction(Request $request){
        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value= hash_hmac('sha256',$str,'hteinlinaungt42');
        $data=[
            'status' => 'success',
            'hash_value' => $hash_value
        ];
        return response()->json($data, 200);
    }

    // myqr
    public function myqr(){
        $user=Auth::guard('web')->user();
        return view('frontend.myqr',compact('user'));
    }

    // payqr
    public function payqr(){
        return view('frontend.payqr');
    }

    // scan transferpage
    public function transferScan(Request $request){

        $user = Auth::guard('web')->user();

        $from_account = Auth::guard('web')->user()->phone;
        $to_phone=User::where('phone',$request->to_phone)->select('phone','name')->first();
        if(!$to_phone){
            return back()->withErrors(['fail'=>'Qr code is invalid'])->withInput();
        }
        if($from_account == $to_phone->phone){
            return back()->withErrors(['fail'=> "You cannot transfer yourself!"])->withInput();
        }


        return view('frontend.transferScan',compact('from_account','to_phone','user'));

    }

    // scan transfer comfrim
    public function transfer_complete_Scan(Request $request)
    {

        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return back()->withErrors(['fail' => 'Something is Wrong!']);
        }

        $user = Auth::guard('web')->user();
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return back()->withErrors(['fail' => 'The recieve phone number is invalid!']);
        }
        if ($user->phone == $request->to_phone) {
            return redirect()->route('user.transfer')->withErrors(['to_phone' => "You can't transfer money to yourself!"]);
        }
        if ($user->wallet->amount < $request->amount) {
            return back()->withErrors(['fail' => "Balance Enough!"]);
        }

        $from_account = $user;
        $to_account = $check_tophone;
        $amount = $request->amount;
        $description = $request->description;




        if (!$from_account->wallet || !$to_account->wallet) {
            return back()->withErrors(['fail' => "Something Wrong!"]);
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
            return redirect()->route('user.home')->with(['success' => "You have been transfer money Successfully!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Something went wrong']);
        }
    }


    public function transferComfirmScan(TransferFormValidate $request)
    {

        $str =$request->to_phone.$request->amount.$request->description;
        $hash_value1= hash_hmac('sha256',$str,'hteinlinaungt42');

        if($hash_value1 !== $request->hash_value){
            return back()->withErrors(['to_phone' => 'Something is Wrong!']);
        }


        $user = Auth::guard('web')->user();
        $check_tophone = User::where('phone', $request->to_phone)->first();
        if (!$check_tophone) {
            return back()->withErrors(['to_phone' => 'The recieve phone number is invalid!']);
        }
        if ($user->phone == $request->to_phone) {
            return back()->withErrors(['to_phone' => "You can't transfer money to yourself!"]);
        }

        if ($user->wallet->amount < $request->amount) {
            return back()->withErrors(['to_phone' => "Balance Enough!"]);
        }

        if (!$user->wallet || !$check_tophone->wallet) {
            return back()->withErrors(['to_phone' => "Something Wrong!"]);
        }

        $hash_value=$request->hash_value;
        $to_user = $check_tophone->name;
        $to_phone = $request->to_phone;
        $amount = $request->amount;
        $description = $request->description;
        return view('frontend.transfercomfirmScan', compact('user', 'to_phone', 'amount', 'description', 'to_user','hash_value'));
    }
}
