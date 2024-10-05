<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Log\Logger;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use App\Helpers\UUIDGenerate;
use Yajra\DataTables\DataTables;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserPassword;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $User = new User();
            $User->name = $request->name;
            $User->email = $request->email;
            $User->phone = $request->phone;
            $User->password = Hash::make($request->password);
            $User->save();

            Wallet::firstorCreate(
                [
                    'user_id' => $User->id
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0,
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Something went wrong']);
        }

        return redirect()->route('user.index')->with(["successMsg" => "You are created Successfully"]);
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
        $user = User::find($id);
        return view('backend.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        DB::beginTransaction();
        try {

            $user = User::findorFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->update();

            Wallet::firstorCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_number' => UUIDGenerate::accountNumber(),
                    'amount' => 0,
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with(['fail' => 'Something went wrong']);
        }
        return redirect()->route('user.index')->with(["successMsg" => "You are Updated Successfully"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = User::findorFail($id);
        $admin->delete();
        $msg = ['success'];
        return response()->json($msg, 200);
    }

    // datatable
    public function ssd()
    {
        $admin = User::query();
        return DataTables::of($admin)
            ->editColumn('user_agents', function ($each) {
                $agent = new Agent();
                if ($each->user_agent) {
                    $agent->setUserAgent($each->user_agent);
                    $browser = $agent->browser();
                    $platform = $agent->platform();
                    $device = $agent->device();

                    return '
                     <table class="table table-bordered">
                         <tbody>
                             <tr><td class="bg-warning">Device</td><td>' . $device . '</td><tr>
                             <tr><td class="bg-warning">Platform</td><td>' . $platform . '</td><tr>
                             <tr><td class="bg-warning">Broswer</td><td>' . $browser . '</td><tr>
                         <tbody>
                     </table>';
                }
                return '_';
            })
            ->editColumn('created_at', function ($each) {
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('updated_at', function ($each) {
                return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
            })
            ->editColumn('ip_address', function ($each) {
                return ($each->ip_address) ? $each->ip_address : '_';
            })
            ->addColumn('actions', function ($each) {
                $edit = '<a href="' . route('user.edit', $each->id) . '" class="text-primary shadow p-2"><i class="fa-regular fa-pen-to-square p-1 fw-bold"></i></a>';
                $delete = '<a href="#" class=" delete_btn text-danger shadow  p-2" data-id="' . $each->id . '" ><i class="fa-solid fa-trash p-1 fw-bold"></i></a>';
                return '<div class="d-flex justify-content-center">' .
                    $edit . $delete
                    . '</div>';
            })
            ->rawColumns(['actions', 'user_agents'])
            ->make(true);
    }

    //  profile
    public function profile()
    {
        $user = Auth::guard('web')->user();
        return view('frontend.profile', compact('user'));
    }

    public function changepasswordpage()
    {
        return view('frontend.updatepassword');
    }
    // updatepassword
    public function changepassword(UpdateUserPassword $request)
    {
        $user = Auth::guard('web')->user();
        $oldpassword = User::where('id', $user->id)->first()->password; // Fetch the password attribute directly
        if (Hash::check($request->oldpassword, $oldpassword)) {
            $data = [
                'password' => Hash::make($request->newpassword),
            ];

            User::where('id', $user->id)->update($data);
            $title= 'Changed Password!';
            $message = 'Your account password is successfully changed.';
            $sourceable_id = $user->id;
            $sourceable_type = User::class;
            $web_link = url('/user/profile');
            Notification::send($user, new GeneralNotification($title,$message,$sourceable_id,$web_link,$sourceable_type));

            Auth::guard('web')->logout();
            return redirect()->route('user.index');
        } else {
            return back()->with(['fail' => 'Old password does not match']);
        }
    }
}
