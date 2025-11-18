<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('Login/login');
    }

    public function auth(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);
        $infologin = [
            'username' => $request->username,
            'password' => $request->password
        ]; 
        if(Auth::attempt($infologin)){
            $request->session()->regenerate();
            // return 'sukses';
            return redirect()->intended('/dashboard');
        }else{
            return 'gagal';
        }
        
    }
}
