<?php

namespace App\Http\Controllers;


use Auth;
use App\Models\User;

use Illuminate\Http\Request;
use App\Models\Employeemanager;

use App\Models\Profile;
use App\Traits\CaptureIpTrait;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use jeremykenedy\LaravelRoles\Models\Role;
use Validator;

class EmployeemanagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $users = User::all();
        $employeemanager = Employeemanager::all();

        return view('employeemanager.index', compact(['users', 'employeemanager']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        echo $request->manager_user_id;

        $delete_manager_employees = Employeemanager::where('manager_user_id', $request->manager_user_id)->delete();

        if($request->employee_user_id) {
            foreach ($request->employee_user_id as $new_employee_id) {
                $em = new Employeemanager;
                $em->manager_user_id = $request->manager_user_id;
                $em->employee_user_id = $new_employee_id;
                $em->save();
            }
        }

        return redirect('/employeemanager');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
