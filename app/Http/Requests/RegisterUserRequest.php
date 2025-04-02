<?php

namespace App\Http\Requests;

use App\Services\AuthService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterUserRequest extends FormRequest
{
    public function authorize(AuthService $service): bool
    {
        return $service->checkRegistrationToken(
            $this->header('Token', ''),
            $this->getClientIp(),
            $this->userAgent(),
        );
    }

    public function rules(): array
    {
        $width = (int) config('app.tinify_crop_width');
        $height = (int) config('app.tinify_crop_height');

        return [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc', // |unique:users,email
            'phone' => 'required|string|regex:/^\+380[0-9]{9}$/', // |unique:users,phone - need canonical
            'position_id' => 'required|integer|exists:positions,id',
            'photo' => 'required|image|mimes:jpg,jpeg|dimensions:min_width='.$width.',min_height='.$height.'|max:5120' // JPG/JPEG, min 70x70, max 5MB
        ];
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'The token expired.'
            ], 401)
        );
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'fails' => $validator->errors()
            ], 422)
        );
    }
}