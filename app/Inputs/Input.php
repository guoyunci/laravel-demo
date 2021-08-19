<?php
/** @noinspection PhpMissingParamTypeInspection */

namespace App\Inputs;

use App\CodeResponse;
use App\Exceptions\BusinessException;
use App\VerifyRequestInput;
use Illuminate\Support\Facades\Validator;

class Input
{
    use VerifyRequestInput;

    /**
     * @param  null|array  $data
     * @return Input|static
     * @throws BusinessException
     */
    public static function new($data = null): Input
    {
        return (new static())->fill($data);
    }

    /**
     * @param  null|array  $data
     * @return $this
     * @throws BusinessException
     */
    public function fill($data = null): Input
    {
        if (is_null($data)) {
            $data = request()->input();
        }

        $validator = Validator::make($data, $this->rules());
        if ($validator->fails()) {
            // dd($validator->getMessageBag());
            throw new BusinessException(CodeResponse::PARAM_VALUE_ILLEGAL);
        }

        $map = get_object_vars($this);
        $keys = array_keys($map);
        collect($data)->map(function ($v, $k) use ($keys) {
            if (in_array($k, $keys)) {
                $this->$k = $v;
            }
        });
        return $this;
    }

    public function rules(): array
    {
        return [];
    }
}
