<?php

namespace App\Http\Requests\API;

use App\Trait\ResponseHandler;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PostAddRequest extends FormRequest
{
    use ResponseHandler;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'                 => 'required|string|max:255',
            'description'           => 'nullable|string',
            'category_id'           => 'required|uuid|exists:categories,id',
            'subcategory_id'        => 'required|uuid|exists:categories,id',
            'sub_subcategory_id'    => 'required|uuid|exists:categories,id',
            'price'                 => 'required|numeric|min:0',
            'work_type'             => 'nullable|string',
            'location'              => 'required|array',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['errors' => $this->errorProcessor($validator)],422));
    }
}
