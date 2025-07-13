<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RefreshRequest;
use Illuminate\Support\Facades\Session;
use Laravel\Passport\RefreshTokenRepository;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function signUp(SignupRequest $request): JsonResponse
    {

        try {

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'type' => (int) $request->type,
            ];

            //creating a new user
            $user = User::createUser($data);
            info($user);
            //generate access token using helper function
            $tokenData = generateAccessToken($user, $request->password);

            //check if token generation failed
            if (! $tokenData) {
                //delete the user if token generation fails
                $user->delete();

                return ApiResponse::setMessage('Token generation failed')
                    ->response(Response::HTTP_BAD_REQUEST);
            }

            Auth::login($user);

            // Start a session manually
            if (! Session::isStarted()) {
                Session::start();
            }

            return ApiResponse::setMessage('User Created Successfully')
                ->mergeResults($tokenData)
                ->response(Response::HTTP_CREATED);

        } catch (Throwable $e) {

            return ApiResponse::setMessage($e->getMessage())
                ->response(Response::HTTP_BAD_REQUEST);
        }

    }

    public function login(LoginRequest $request): JsonResponse
    {

        try {
            $credentials = request(['email', 'password']);

            // validate the user
            $user = User::where('email', $credentials['email'])->first();

            if (! $user || ! Hash::check($credentials['password'], $user->password)) {
                return ApiResponse::setMessage('Unauthenticated')
                    ->response(Response::HTTP_UNAUTHORIZED);
            }

            //generate access token using helper function
            $tokenData = generateAccessToken($user, $request->password);

            //checking if token generation failed
            if (isset($tokenData['error'])) {
                return ApiResponse::setMessage($tokenData['error'])
                    ->response(Response::HTTP_BAD_REQUEST);
            }

            // Login the user
            Auth::login($user);

            // Start a session manually
            if (! Session::isStarted()) {
                Session::start();
            }

            $roles = $user->type; // Assuming roles have a 'name' attribute
            info(json_encode($roles));
            // Success response if tokens are generated successfully
            return ApiResponse::setMessage('Successfully logged in')
    ->mergeResults([
        ...$tokenData,
        'role' => $user->type->value, // Convert enum to int
    ])
    ->response(Response::HTTP_OK);


        } catch (Throwable $e) {
            return ApiResponse::setMessage($e->getMessage())
                ->response(Response::HTTP_BAD_REQUEST);
        }

    }

    // Revoke the access token
    public function logout(Request $request)
    {
        //destroying the session
        Session::invalidate();

        //regenerate the session ID to prevent session fixation attacks
        Session::regenerateToken();

        $user = Auth::user();
        $tokens = Auth::user()->tokens;
        // Check if user exists and has an active token
        if ($user && $tokens) {

            foreach ($tokens as $token) {
                $token->revoke();
                $refreshTokenRepository = app(RefreshTokenRepository::class);
                $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($token->id);
            }

            return ApiResponse::setMessage('Successfully logged out')
                ->response(Response::HTTP_OK);

        } else {
            return ApiResponse::setMessage('No active access token found for the user')
                ->response(Response::HTTP_BAD_REQUEST);
        }

    }

    public function refreshAccessToken(RefreshRequest $request)
    {

        $refreshToken = $request->refresh_token;

        try {
            $tokenData = refreshAccessToken($refreshToken);

            // Return the response (new access and refresh tokens)
            return ApiResponse::setMessage('Tokens Successfully created!')
                 ->mergeResults($tokenData)
                ->response(Response::HTTP_OK);

        } catch (Throwable $e) {
            return ApiResponse::setMessage('The refresh token is invalid or expired')
                ->response(Response::HTTP_BAD_REQUEST);
        }

    }
}