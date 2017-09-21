<?php

namespace App;

// Illumiante
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Tree extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Load with relations.
     *
     * @var array
     */
    protected $with = [
        // 'branches:id',
    ];

    /**
     * Load relations.
     *
     * @var array
    */
    static public $load_relations = [
        'branches:tree_id,parent_id,id'
    ];

    /**
     * Return a hasMany relation to App\Branch.
     *
     * @return App\Branch
     */
    public function branches()
    {
        return $this->hasMany('App\Branch', 'tree_id');
    }

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
             ],
         ];

        // merge default rules with parameter rules
        $rules = array_merge($default_rules, $rules);

        return Validator::make($data, $rules);
    }

    public static function allWithRelations() {
        return static::with(static::$load_relations)->get();
    }
    
    public function withRelations() {
        return $this->load(static::$load_relations);
    }
}
