<?php

namespace App;

// Illuminate
use Illuminate\Database\Eloquent\Model;
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
        $default_rules = [];

        // merge default rules with parameter rules
        $rules = array_merge($default_rules, $rules);

        return Validator::make($data, $rules);
    }
}
