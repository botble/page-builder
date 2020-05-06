<?php

namespace Botble\PageBuilder\Http\Requests;

use Botble\Support\Http\Requests\Request;

class PageBuilderRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content' => 'required',
        ];
    }
}
