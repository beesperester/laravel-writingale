<?php

namespace App;

// Illumiante
use Illuminate\Support\Facades\Validator;

class Branch extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'parent_id',
        'tree_id',
        'sorting',
    ];

    /**
     * Load with relations.
     *
     * @var array
     */
    protected $with = [
        // 'branches',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'depth',
    ];

    /**
     * Load relations.
     *
     * @var array
     */
    public static $load_relations = [
        'branches:tree_id,parent_id,id',
        'tree:id',
    ];

    /**
     * Return a belongsTo relation to App\Branch.
     *
     * @return App\Branch
     */
    public function parent()
    {
        return $this->belongsTo('App\Branch', 'parent_id');
    }

    /**
     * Return a belongsTo relation to App\Tree.
     *
     * @return App\Tree
     */
    public function tree()
    {
        return $this->belongsTo('App\Tree', 'tree_id');
    }

    /**
     * Return a hasMany relation to App\Branch.
     *
     * @return App\Branch
     */
    public function branches()
    {
        return $this->hasMany('App\Branch', 'parent_id');
    }

    public function getDepthAttribute()
    {
        if ($this->parent_id) {
            return Branch::find($this->parent_id)->depth + 1;
        }

        return 0;
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
        $default_rules = [];

        if (!isset($data['tree_id'])) {
            $rules['parent_id'] = [
                'required',
            ];
        }

        if (!isset($data['parent_id'])) {
            $rules['tree_id'] = [
                'required',
            ];
        }

        // merge default rules with parameter rules
        $rules = array_merge($default_rules, $rules);

        return Validator::make($data, $rules);
    }

    public static function allWithRelations()
    {
        return static::with(static::$load_relations)->get();
    }

    public function withRelations()
    {
        return $this->load(static::$load_relations);
    }
}
