<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function paginate()
    {
        $users = User::orderBy('name', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $users->appends(request()->all());
        }

        return response()->json($users);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role'          => 'required|in:ADMIN,PENELITI'
        ];

        $oldUser = null;
        if($id != null){
            $rules['email'] = 'required|email';
            $rules['password'] = 'nullable|min:6';
            $oldUser = User::find($id);
            if($oldUser->email != $req->input("email")) $rules['email'] .= "|unique:users,email";
        }
        $this->validate($req, $rules);

        if(!$oldUser)
            $user = new User();
        else $user = $oldUser;
        $user->name     = $req->input('name');
        $user->email    = $req->input('email');
        $user->role     = $req->input('role');
        if($req->input("password") !== null)
            $user->password = Hash::make($req->input('password'));
        $user->save();

        return response()->json($user);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    public function delete($id){
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([]);
    }
}
