<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class WalletController extends Controller
{
    public function index(){
        return view('backend.wallet.index');
    }

     // datatable
     public function ssd()
     {
         $wallet=Wallet::with('user');
         return DataTables::of($wallet)
            ->editColumn('account_preson',function($each){
               if($each->user){
                    $name=$each->user->name;
                    $email=$each->user->email;
                    $phone=$each->user->phone;
                    return '<div class="text-start"> <p>Name : '. $name .'</p>'.'<p>Email : '. $email .'</p>'.'<p>Phone : '. $phone .'</p></div>';
                }
                return '_';
             })
             ->editColumn('amount',function($each){
                return number_format($each->amount,2);
             })
             ->editColumn('created_at',function($each){
                 return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
              })
              ->editColumn('updated_at',function($each){
                 return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
              })
              ->rawColumns(['account_preson'])
             ->make(true);
     }

    //  userwallet
    public function userwallet(){
        $user=Auth::guard('web')->user();
        return view('frontend.wallet',compact('user'));
    }

    public function addamount(){
        $users=User::all();
        return view('backend.wallet.addamount',compact('users'));
    }

    public function addamountstore(Request $request){
        $amount = $request->amount;
        $id = $request->user;
        $description = $request->description;
        $to_account=User::findorFail($id);
        DB::beginTransaction();
        try {

            $to_account_wallet = $to_account->wallet;
            $to_account_wallet->increment('amount', $amount);
            $to_account_wallet->update();

            $ref_no = UUIDGenerate::refNumber();

            $to_account_transactions= new Transaction();
            $to_account_transactions->ref_no=$ref_no;
            $to_account_transactions->trx_id=UUIDGenerate::trxId();
            $to_account_transactions->user_id=$to_account->id;
            $to_account_transactions->type=1;
            $to_account_transactions->amount=$amount;
            $to_account_transactions->source_id=0;
            $to_account_transactions->description=$description;
            $to_account_transactions->save();


            // // send noti for to_account
            $title= 'E_Money Received!';
            $message = 'Your e_money received ' . $amount . ' MMK to ' . Auth::guard('admin_user')->user()->name .'('.Auth::guard('admin_user')->user()->phone.')';
            $sourceable_id = Auth::guard('admin_user')->user()->id;
            $sourceable_type = Transaction::class;
            $web_link = url('/transaction/'.$to_account_transactions->trx_id);
            Notification::send($to_account, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));

            DB::commit();
            return redirect()->route('wallet')->with(['success' => "You have been added money Successfully!"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Something went wrong']);
        }
    }
}
