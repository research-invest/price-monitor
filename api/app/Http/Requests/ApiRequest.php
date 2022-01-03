<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ApiRequest
 * @package App\Http\Requests
 */
abstract class ApiRequest extends FormRequest
{

    protected function prepareForValidation()
    {
        $data = [];

        if (is_array($this->filter)) {
            foreach ($this->filter as $filter => $value) {
                if (!empty($this->filter[$filter])) {
                    $data['filter'][$filter] = is_string($this->filter[$filter]) ? explode(',', $this->filter[$filter]) : $this->filter[$filter];
                }
            }
        }

        $this->merge($data);
    }

    protected function passedValidation()
    {
        $data = $this->validated();
        $this->replace($data);
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 400));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\InputBag|\Symfony\Component\HttpFoundation\ParameterBag|null
     */
    protected function getInputSource()
    {
        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }
}
