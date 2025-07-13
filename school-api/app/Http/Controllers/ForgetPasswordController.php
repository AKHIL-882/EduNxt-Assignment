<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ForgetPasswordTokens;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ForgetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request)
    {

        $email = $request->email;
        //find the user by email
        $user = User::where('email', $email)->firstOrFail();

        $user->initiatePasswordReset();

        return ApiResponse::setMessage('Reset link sent to your email')
            ->response(Response::HTTP_OK);

    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $token = $request->token;

        //Find the token record
        $forgetPasswordToken = ForgetPasswordTokens::where('token', $token)->firstOrFail();

        if (! $forgetPasswordToken) {
            return ApiResponse::setMessage('Invalid password reset request')
                ->response(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $result = $forgetPasswordToken->updatePassword($request->password);

        return ApiResponse::setMessage($result['message'])
            ->response($result['status'] ? Response::HTTP_OK : Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}