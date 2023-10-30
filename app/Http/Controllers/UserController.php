<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
use App\Http\Requests\UpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;


class UserController extends Controller
{
    /**
     * Method index
     *
     * show registration form
     *
     * @return void
     */
    public function index()
    {
        return view('pages.register');
    }

    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        if ($data) {
            $user = User::create($data);
            Mail::to($user->email, 'godamit')->send(new VerificationMail($user));
            session()->flash('status_message', "Thank you for registering, check your email for verification instructions");

            return redirect('/');
        }
        session()->flash('status_message', "Invalid Data");

        return redirect('/register');
    }

    /**
     * Method validateEmail
     *
     * @param $id $id takes id of user to activate user account
     *
     * @return void
     */
    public function validateEmail($id)
    {
        $user = User::findOrFail($id);

        $user->is_verified = true;

        $user->remember_token =  Str::random(10);

        $user->email_verified_at = now();

        $user->save();

        return redirect('/login');
    }

    /**
     * Method verification
     *
     * @return veiw with resend email address in case you didn't received email
     */
    public function verification()
    {

        return view('pages.user.verify-email');
    }

    /**
     * Method resendEmail
     *
     * resend email to user
     *
     * @return void
     */
    public function resendEmail()
    {
        $user = Auth::user();
        Mail::to($user->email, 'godamit')->send(new VerificationMail($user));
            session()->flash('status_message', "Email sent again, check your email for verification instructions");
        return redirect()->back();
    }

    /**
     * Method login
     *
     * @return login form
     */
    public function login()
    {
        return view('user.pages.login');
    }

    /**
     * Method logOn
     *
     * @param Request $request email and password from Request object
     *
     * @return logged in user or error if invalid credentials
     */
    public function logOn(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $query = Gallery::query();
            $galleries = $query
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
            session()->put('user_id', $user->id);
            return redirect('/')->with(compact('user', 'galleries'));
        }
        return redirect()->back()->withErrors('invalid credentials');
    }

    /**
     * Method logOff
     *
     * @return logOff user
     */
    public function logOff()
    {

        Auth::logout();
        session()->flush();
        return redirect('/');
    }

    /**
     * Method profile
     *
     * @param $id takes $id to find user profile
     *
     * show user profile where he can update his personal informations
     *
     * @return void
     */
    public function profile($id)
    {
        $user = Auth::user();
        return view('pages.user.change-password', compact('user'));
    }

    /**
     * Method update
     *
     * @param UpdateRequest $request Object with id
     *
     * find user and update information
     *
     * @return void
     */
    public function update(UpdateRequest $request)
    {
        // dd($request);
        $id = Auth::user()->id;
        $user = User::find($id);
        $valid = $request->validated();
        if ($valid) {
            foreach ($valid as $key => $value) {
                if ($value === null || $value === '') {
                    unset($valid[$key]);
                }
            }
            $user->update($valid);
            session()->flash('status_message', "Profile updated successfully");
            return redirect()->back();
        }
    }
}
