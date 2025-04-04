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
        $client_token = $this->header('Token', '');

        return $service->checkRegistrationToken($client_token, [
            // 'client_id' => $this->getClientIp(),
            // 'client_agent' => $this->userAgent(),
        ]);
    }

    public function rules(): array
    {
        $width = (int) config('app.tinify_crop_width');
        $height = (int) config('app.tinify_crop_height');

        return [
            'name' => 'required|string|min:2|max:60',
            'email' => [
                'required',
                'min:6',
                'max:100',
                'regex:/^(?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/',
            ],
            'phone' => 'required|string|regex:/^[\+]{0,1}380([0-9]{9})$/',
            'position_id' => 'required|integer|exists:positions,id',
            'photo' => 'required|image|mimes:jpg,jpeg|dimensions:min_width='.$width.',min_height='.$height.'|max:5120'
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