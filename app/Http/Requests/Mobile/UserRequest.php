<?php

namespace App\Http\Requests\Mobile;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required|max:100',
            'email'    => 'required|email|unique:ec_users,email',
            'password' => 'required|min:8|max:36',
            'phone'    => 'required|size:10|unique:ec_users,phone'
        ];
    }
}
