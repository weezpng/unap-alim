<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class editUserSettings extends FormRequest
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
          'inputName' => 'required|string:max:50',
          'inputPosto' => 'required',
          'inputUEO' => 'required'
      ];
    }

    public function messages()
    {
        return [
            'inputName.required' => 'Preencha o seu Nome',
            'inputPosto.required' => 'Preencha o seu Posto',
            'inputUEO.required' => 'Preencha com a sua Unidade'
        ];
    }
}
