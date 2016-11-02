<?php

namespace App\Political\Elections;

use App\Http\Requests\Request;

class ElectionRequest extends Request
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
            'pin_number'  => 'required|min:5',
            'document_number'  => 'required',
            'registed_voters'  => 'required',
            'valid_votes'  => 'required',
            'invalid_votes'  => 'required',
            'affidavit_votes'  => 'required',
            'total_votes'  => 'required',
            'region'    => 'required|integer',
            'state'    => 'required|integer',
            'city'  => 'required|integer',
            'subcity'  => 'required|integer',
            'county'  => 'nullable',
            'pollsite'  => 'required|integer',
            'precint'  => 'required|integer',

        ];
    }
}
