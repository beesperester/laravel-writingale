<?php

namespace App;

// Illuminate
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BaseModel extends Model
{
    /**
     * Create new validator from data and rules.
     *
     * @param array $data
     * @param array $rules
     *
     * @return Illuminate\Support\Facades\Validator;
     */
    public static function getValidator(array $data = [], array $rules = [])
    {
        $default_rules = [
            'name' => [
                'required',
                Rule::unique('trees')->where(
                    function ($query) use ($data) {
                        // check against own id
                        if (isset($data['id'])) {
                            $query->where('id', '!=', $data['id']);
                        }
                    }
                ),
            ]
        ];

        // merge default rules with parameter rules
        $rules = array_merge($default_rules, $rules);

        return Validator::make($data, $rules);
    }
}