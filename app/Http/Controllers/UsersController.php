<?php

namespace App\Http\Controllers;

use DB;
use Auth;

use Carbon\Carbon;
use App\Services\Helpers;
use Illuminate\Http\Request;

use App\Events\PasswordResetRequested;

use App\Models\User;
use App\Models\SecurityQuestion;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['showAccountPage', 'showActivationPage', 'updateAccount']]);
    }
    /**
     * Show page for logging user in
     */
    public function showLoginPage()
    {
        return view('auth.login');
    }

    public function showRegisterPage()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {

        $customMessages = [
        'name.required' => 'Kolom nama harus diisi.',
        //'name.string' => 'Kolom nama harus berupa string.',
        'name.max' => 'Maximum karakter nama adalah 30.',
        'email.required' => 'Kolom email harus diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.max' => 'Maximum karakter email 25.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.required' => 'Kolom pasword harus diisi.',
       // 'password.string' => 'The password must be a string.',
        'password.min' => 'Minimum karakter password adalah 8.',
        'password.confirmed' => 'Password tidak sesuai.',
        ];

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:30',
            'email' => 'required|string|email|max:25|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'lvl'=>'required',
        ],$customMessages);

        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'lvl' => $validatedData['lvl'],
        ]);

        // Return a response
        return redirect('/login');
    }

    /**
     * Log in a user
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function loginUser(Request $request)
    {
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];

        $this->validate($request, $rules);

         $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['belum ada akun user']);
        }

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password salah']);
        }

        Auth::login($user);

        return redirect('/');
    }

    /**
     * Show account activation page where new user can set up his
     * account
     *
     * @return Illuminate\Http\Response Account activation view
     */
    public function showActivationPage()
    {
        $user = Auth::user();
        $questions = SecurityQuestion::all();

        return view('users.activate', compact('user', 'questions'));
    }

    /**
     * Activate and set up account for user
     *
     * @param Illuminate\Http\Request $request The HTTP request
     * @return Illuminate\Http\Response Redirect to home page
     */
    public function activateUser(Request $request)
    {
        $user = Auth::user();

        if ($user->activated) {
            return redirect()->back()->withError('akun sudah aktif');
        }

        $rules = [
            'name' => 'required',
            'password' => 'required|confirmed',
            'security_question_id' => 'required|exists:security_questions,id',
            'security_question_answer' => 'required'
        ];

        $messages = [
            'security_question_id.required' => 'A security question must be selected.',
            'security_question_answer.required' => 'Add an answer for security question.'
        ];

        $this->validate($request, $rules, $messages);

        $user->update([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'security_question_id' => $request->security_question_id,
            'security_question_answer' => $request->security_question_answer,
            'activated' => true
        ]);

        return redirect('/');
    }

    /**
     * Show the page to reuqest new password
     */
    public function showPasswordRequestPage()
    {
        $user = User::first();

        return view('users.password_request', compact('user'));
    }

    /**
     * Handle the request to reset password after user forgets
     * password
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function requestPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'security_question_answer' => 'required'
        ];

        $this->validate($request, $rules);

        $user = User::first();

        if ($user->security_question_answer != $request->security_question_answer) {
            return redirect()->back()->withErrors(['jawaban salah']);
        }

        $token = Helpers::generateRandomString();

        DB::table('password_resets')->delete();
        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'expiry_date' => Carbon::now()->addDay()->toDateTimeString()
        ]);

        event(new PasswordResetRequested($token, $request->email));

        return redirect('/reset_password');
    }

    /**
     * Show page for password reset
     *
     */
    public function showResetPassword()
    {
        return view('users.password_reset');
    }

    /**
     * Handle reset of password
     */
    public function resetPassword(Request $request)
    {
        $token = DB::table('password_resets')->first();

        $rules = [
            'token' => 'required'
        ];

        $this->validate($request, $rules);

        if (!$token || ($token && $token->token != $request->token)) {
            return redirect()->back()->withErrors(['Invalid token']);
        }

        if (Carbon::now()->gt(Carbon::parse($token->expiry_date))) {
            return redirect()->back()->withErrors(['Token has expired.Please request for new token']);
        }

        $user = User::first();
        $user->update([
            'activated' => false
        ]);

        Auth::login($user);
        return redirect('/');
    }

    /**
     * Show account settings page
     *
     */
    public function showAccountPage()
    {
        $user = Auth::user();
        $questions = SecurityQuestion::all();

        return view('users.account', compact('user', 'questions'));
    }

    /**
     * Update user account
     *
     * @param Illuminate\Http\Request $request The HTTP request
     */
    public function updateAccount(Request $request)
    {
        $rules = [
            'name' => 'required|max:30',
           // 'email' => 'required|email|unique:users,email,' . Auth::user()->id,
            'security_question_id' => 'required',
            'security_question_answer' => 'required'
        ];

        if ($request->has('password') && $request->password) {
            $rules['password'] = 'confirmed';
            $rules['old_password'] = 'required';
        };

         $messages = [
         'name.max' =>'maximal karakter nama 30'
         ];

        $this->validate($request, $rules, $messages);

        $user = Auth::user();
        $data = [
            'name' => $request->name,
            'security_question_id' => $request->security_question_id,
            'security_question_answer' => $request->security_question_answer
        ];

        if ($request->has('password') && $request->password) {
            if (!Hash::check($request->old_password, $user->password)) {
                return redirect()->back()->withErrors(['password lama salah']);
            }

            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('status', 'akun diupdate');
    }
}