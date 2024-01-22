<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPasswordMail;
use App\Models\Password_resets;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api')->except('login', 'refreshToken','forgetPassword','checkToken','emailExist','resetPassword');
    }

    public function register(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|min:8|max:20|unique:users',
        ])->validate();

        return $this->userService->register($request->all());
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ])->validate();

        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->error('Unauthorized', 401);
        }

        return  $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'user' => auth()->user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    public function profile(Request $request)
    {
        return $this->userService->profile(auth()->user()->id);
    }

    public function refreshToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();

        return $this->respondWithToken($newToken);
    }

    public function emailExist($email)
    {
        $data = ['email' => $email];
        $validator = \Validator::make( $data , [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);
        if ($validator->fails()) {
                     // Handle validation errors here
            return response()->json(['errors' => $validator->errors()], 422);
        }


        return response()->success('', 'Email exists');
    }

    public function logout()
    {
        auth()->logout();

        return response()->success('', 'Successfully logged out');
    }

    public function updateProfile(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:50',
            'last_name' => 'required|string|min:2|max:50',
            'image_url' => 'nullable|string',
            'age' => 'nullable|string',
            'gender' => 'nullable|string',
        ])->validate();

        return $this->userService->updateProfile(auth()->user()->id, $request->all());
    }

    public function updatePassword()
    {
        $validator = \Validator::make(request()->all(), [
            'old_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ])->validate();

        return $this->userService->updatePassword(auth()->user()->id, request()->all());
    }

    protected function tokenUniqueCheck ($token)
    {
        $validator = \Validator::make(['token' => $token], [
            'token' => 'required|string|unique:password_resets,token',
        ]);

        if ($validator->fails()) {
            $token = bin2hex(random_bytes(32)); // Generate a random token
            $this->tokenUniqueCheck($token);
        }

        return true;
    }

    public function forgetPassword(Request $request)
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|string|max:255|exists:users,email',
        ]);

        // validation fails
        if ($validator->fails()) {
            return response()->error(['errors' => $validator->errors()], 422);
        }

        $emailAddress = $request->input('email');

        $tokenWithOutEncrypted = bin2hex(random_bytes(32)); // Generate a random token
        $token = bcrypt($tokenWithOutEncrypted);

        $tokenUnique = $this->tokenUniqueCheck($token);

        if ($tokenUnique === true) {
            $expiresAt = now()->addMinutes(30); // Set expiration time to 10 minutes from now
           // check if there is a token for this email then update it
            $tokenModel = Password_resets::where('email', $emailAddress)->first();
            if ($tokenModel) {
                $tokenModel->token = $token;
                $tokenModel->expires_at = $expiresAt;
                $tokenModel->is_used = false;
                $tokenModel->save();
            }
            else {
                $tokenModel = new Password_resets();
                $tokenModel->email = $emailAddress;
                $tokenModel->token = $token;
                $tokenModel->expires_at = $expiresAt;
                $tokenModel->save();
            }

            // Send the email
            Mail::to($emailAddress)->send(new ForgetPasswordMail($tokenWithOutEncrypted, $tokenModel->id));

            return response()->success('', 'Email sent successfully');
        }

    }

    public function checkToken ($token) {
        // token is encrypted so we need to decrypt it
        $id = request()->get('id');

        $tokenModel = Password_resets::where('id', $id)->where('expires_at', '>', now())->where('is_used',false)->first();

        if (! $tokenModel) {
            return response()->json(['message' => 'Token is Expired']);
        }
        $varify = password_verify($token, $tokenModel->token);

        if ($varify) {
            return response()->success( $tokenModel, 'Token is valid');
        }
        else {
            return response()->json(['message' => 'Token is invalid']);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = \Validator::make(request()->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8',
            'id' => 'required|integer',
        ]);

        // validation fails
        if ($validator->fails()) {
            return response()->error(['errors' => $validator->errors()], 422);
        }

        $token = $request->input('token');
        $password = $request->input('password');
        $id = $request->input('id');

        $tokenModel = Password_resets::where('id', $id)->where('expires_at', '>', now())->where('is_used',false)->first();

        if (! $tokenModel) {
            return response()->error(['message' => 'Token is Expired'], 500);
        }

        $varify = password_verify($token, $tokenModel->token);

        if ($varify) {
            $user = User::where('email', $tokenModel->email)->first();
            $user->password = bcrypt($password);
            $user->save();

            $tokenModel->is_used = true;
            $tokenModel->save();
            return response()->success('', 'Password changed successfully');
        }
        else {
            return response()->json(['message' => 'Token is invalid']);
        }
    }
}
