<?php

namespace App\Http\Requests\TgService;

use App\Http\Requests\ApiRequest;

class MessageRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'chat_id' => 'required|integer',
            'command' => 'string|nullable',
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'username' => 'min:4|nullable',
            'text_message' => 'required|string',
//            'text_message' => ['required', 'string'], //, 'check_market_url'
        ];
    }

    public function withValidator($validator)
    {
        $validator->addExtension('check_market_url', function ($attribute, $value, $parameters, $validator) {
            if (filter_var($value, FILTER_VALIDATE_URL) === false) {
                return false;
            }

            //todo check product!
            $host = parse_url($value, PHP_URL_HOST);

            return !in_array($host, ['wildberries', 'ozon'], true); // todo to const
        });

        $validator->addReplacer('check_market_url', function ($message, $attribute, $rule, $parameters, $validator) {
            return __("The :attribute can't be correct url address. (wildberries or ozon)", compact('attribute'));
        });
    }

}
