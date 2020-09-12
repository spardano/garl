<?php

namespace App\Http\Controllers;

use App\Mclass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClassController extends Controller
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
        $users = Mclass::orderBy('class', 'ASC')
            ->paginate(request('limit', 20));

        if (request()->all()) {
            $users->appends(request()->all());
        }

        return response()->json($users);
    }

    public function save(Request $req, $id = null)
    {
        $rules = [
            'class'          => 'required|string|max:100',
            'color'          => 'required|string|max:7',
        ];

        $oldMclass = null;
        $this->validate($req, $rules);

        $class = null;
        if($id!= null) $class = Mclass::findOrFail($id);

        if(!$class) $class = new Mclass();

        $class->class     = $req->input('class');
        $class->color     = $req->input('color');
        $class->save();

        return response()->json($class);
    }

    public function show($id)
    {
        $class = Mclass::findOrFail($id);

        return response()->json($class);
    }

    public function all(Request $req)
    {
        $class = Mclass::orderBy('class', 'ASC')->get();

        return response()->json($class);
    }
    
    public function delete($id){
        $class = Mclass::findOrFail($id);
        $class->delete();

        return response()->json([]);
    }
}
