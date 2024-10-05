<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminUser;
use App\Http\Requests\UpdateAdminUser;
use App\Models\AdminUser;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

use function Laravel\Prompts\table;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.admin_user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.admin_user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminUser $request)
    {
      $adminUser=new AdminUser();
      $adminUser->name=$request->name;
      $adminUser->email=$request->email;
      $adminUser->phone=$request->phone;
      $adminUser->password=Hash::make($request->password);
      $adminUser->save();
      return redirect()->route('admin_user.index')->with(["successMsg"=>"You are created Successfully"]);

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
        $admin=AdminUser::find($id);
        return view('backend.admin_user.edit',compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminUser $request, string $id)
    {
        $admin =AdminUser::findorFail($id);
        $admin->name=$request->name;
        $admin->email=$request->email;
        $admin->phone=$request->phone;
        if($request->password){
            $admin->password=Hash::make($request->password);
        }
        $admin->update();

        return redirect()->route('admin_user.index')->with(["successMsg"=>"You are Updated Successfully"]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin=AdminUser::findorFail($id);
        $admin->delete();
        $msg=['success'];
        return response()->json($msg, 200);
    }


    // datatable
    public function ssd()
    {
        $admin=AdminUser::query();
        return DataTables::of($admin)
            ->editColumn('user_agents',function($each){
                $agent = new Agent();
                if($each->user_agent){
                    $agent->setUserAgent($each->user_agent);
                    $browser = $agent->browser();
                    $platform = $agent->platform();
                    $device = $agent->device();

                    return '
                    <table class="table table-bordered">
                        <tbody>
                            <tr><td class="bg-warning">Device</td><td>'. $device . '</td><tr>
                            <tr><td class="bg-warning">Platform</td><td>'. $platform . '</td><tr>
                            <tr><td class="bg-warning">Broswer</td><td>'.$browser .'</td><tr>
                        <tbody>
                    </table>';
                }
                return '_';
            })
            ->editColumn('created_at',function($each){
                return Carbon::parse($each->created_at)->format('Y-m-d H:i:s');
             })
             ->editColumn('updated_at',function($each){
                return Carbon::parse($each->updated_at)->format('Y-m-d H:i:s');
             })
            ->editColumn('ip_address',function($each){
               return ($each->ip_address) ? $each->ip_address : '_';
            })
            ->addColumn('actions', function ($each) {
                $edit = '<a href="' . route('admin_user.edit', $each->id) . '" class="text-primary shadow p-2"><i class="fa-regular fa-pen-to-square p-1 fw-bold"></i></a>';
                $delete = '<a href="#" class=" delete_btn text-danger shadow  p-2" data-id="' . $each->id . '" ><i class="fa-solid fa-trash p-1 fw-bold"></i></a>';
                return '<div class="d-flex justify-content-center">' .
                    $edit . $delete
                    . '</div>';
            })
            ->rawColumns(['actions','user_agents'])
            ->make(true);
    }
}
