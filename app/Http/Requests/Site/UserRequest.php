<?php

namespace App\Http\Requests\Site;

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
            '_n'  => 'required|max:100',
            '_e'  => 'required|email|unique:ec_users,email',
            '_pw' => 'required|min:8|max:36',
            '_b'  => 'required',
            '_g'  => 'required|boolean',
            '_p'  => 'required|min:10|max:12|unique:ec_users,phone'
        ];
    }


}
