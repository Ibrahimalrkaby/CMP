<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\AdminRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminAuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register a new user and return JWT token.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
        } catch (\Exception $e) {
            return $this->serverError('User creation failed', $e);
        }

        return $this->respondWithToken(
            JWTAuth::fromUser($admin),
            ['user' => $admin, 'message' => 'User registered successfully'],
            201
        );
    }

    /**
     * Authenticate user and return JWT token.
     */
    public function login(Request $request)
    {
        // $validator = $request->Validated;
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);


        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }


        if (!$token = Auth::guard('admin_api')->attempt($request->only('email', 'password'))) {
            return $this->authError('Invalid email or password');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get authenticated user details.
     */
    public function me()
    {
        try {
            $admin = auth()->guard('admin_api')->user();

            if (!$admin) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'user' => $admin
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token invalid',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 401);
        }
    }


    /**
     * Invalidate current token.
     */
    public function logout()
    {
        auth()->guard('admin_api')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Format token response with optional additional data.
     */
    protected function respondWithToken($token, $additionalData = [], $status = 200)
    {
        return response()->json(array_merge([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ], $additionalData), $status);
    }

    /**
     * Handle validation errors.
     */
    protected function validationError($errors)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }

    /**
     * Handle authentication errors.
     */
    protected function authError($message)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Authentication failed',
            'errors' => ['credentials' => [$message]]
        ], 401);
    }

    /**
     * Handle server errors.
     */
    protected function serverError($message, \Exception $e)
    {
        // Log error here if needed
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
