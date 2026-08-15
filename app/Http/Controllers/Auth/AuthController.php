<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller; use App\Models\User; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth; use Illuminate\Validation\Rules\Password;
class AuthController extends Controller {
 public function loginForm(){return view('auth.login');} public function registerForm(){return view('auth.register');}
 public function login(Request $r){$d=$r->validate(['email'=>'required|email','password'=>'required']); if(!Auth::attempt($d,$r->boolean('remember')))return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email'); $r->session()->regenerate(); return redirect()->intended($this->dashboard(Auth::user()->role));}
 public function register(Request $r){$d=$r->validate(['name'=>'required|max:100','email'=>'required|email|unique:users','phone'=>'nullable|max:30','password'=>['required','confirmed',Password::min(8)]]);$d['role']='parent';$u=User::create($d);Auth::login($u);return redirect()->route('parent.dashboard');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('home');}
 private function dashboard($role){return match($role){'mentor'=>route('mentor.dashboard'),'admin'=>route('admin.dashboard'),default=>route('parent.dashboard')};}
}
