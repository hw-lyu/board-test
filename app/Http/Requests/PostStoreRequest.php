<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;

class PostStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'    => 'required|max:255',
            'content'  => 'required',
            'write'    => 'required',
            'password' => 'required|string|min:4',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $allowed     = array_keys($this->rules());
            $input       = $this->validationData();

            $onlyAllowed = array_intersect_key($input, array_flip($allowed));
            $extraFields = array_diff_key($input, $onlyAllowed);

            if (!empty($extraFields)) {
                $validator->errors()->add('extra', '허용되지 않은 필드가 포함되어 있습니다.');
            }
        });
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code'    => 422,
                'message' => '유효성 검증에 실패했습니다.',
                'data'    => $validator->errors(),
            ], 422)
        );
    }
}
