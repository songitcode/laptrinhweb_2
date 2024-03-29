<?php

namespace App\Http\Controllers;

use Hash;
use Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * CRUD User controller
 */
class CrudUserController extends Controller
{

    /**
     * Login page
     */
    public function login()
    {
        return view('auth.login');
    }

    /** User submit form login */
    public function authUser(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->intended('list')
                ->withSuccess('Signed in');
        }

        return redirect("login")->withSuccess('Login details are not valid');
    }

    /**
     * Registration page
     */
    public function createUser()
    {
        return view('auth.create');
    }

    /**
     * User submit form register
     */
    public function postUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        return redirect("list")->withSuccess('You have signed-in');
    }

    /** View user detail */
    public function readUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::find($user_id);

        return view('auth.read', ['user' => $user]);
    }

    public function deleteUser(Request $request)
    {
        $user_id = $request->get('id');
        $user = User::destroy($user_id);

        return redirect("list")->withSuccess('You have signed-in');
    }

    /** List of users */
    public function listUser()
    {
        if (Auth::check()) {
            $users = User::all();
            return view('auth.list', ['users' => $users]);
        }

        return redirect("login")->withSuccess('You are not allowed to access');
    }
    /** Update user detail */
    public function updateUser(Request $request)
    {
        // Lay id bang phuong thuc get
        $user_id = $request->get('id');
        // lenh tim id trong csdl ngan gon
        $user = User::find($user_id);
        // sau khi tim thay id theo phuong thuc tra ve
        // view va tao gia tri user
        return view('auth.update', ['user' => $user]);
    }
    public function postUpdateUser(Request $request)
    {
        // Kiem tra du lieu
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        // lay id tu co so du lieu
        $user_id = $request->get('id');
        // a` anh oi toi tim thay thang id cua anh roi
        $user = User::find($user_id);
    
        // sau do toi tien hanh lay may cai moi cua anh de toi ... ->
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        // ... save no lai
        $user->save();
        // roi toi tra no ve trang list
        return redirect("list")->withSuccess('User details have been updated');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }

}
