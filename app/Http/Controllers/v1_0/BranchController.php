<?php

namespace App\Http\Controllers\v1_0;

// Illuminate
use App\Branch;
use App\Tree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // get data from request
        $data = $request->all();

        // get validator
        $validator = Branch::getValidator($data);

        // validate
        $validator->validate();

        // reorder branches
        $data['sorting'] = static::reorder($request->input('sorting'), $request->input('parent_id'), $request->input('tree_id'));

        // create and return new tree
        $branch = Branch::create($data);

        $query = [
            ['parent_id', '=', $request->input('parent_id')],
            ['tree_id', '=', $request->input('tree_id')],
        ];

        return Tree::all();
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Branch $branch
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        return $branch;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Branch $branch
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Branch              $branch
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        // get data from request
        $data = $request->all();

        // merge old data with new data
        $data = array_merge($branch->toArray(), $data);

        // get validator
        $validator = Branch::getValidator($data);

        // validate
        $validator->validate();

        // reorder branches
        $data['sorting'] = static::reorder($tree, $request->json()->input('sorting', $data['sorting']), $request->json()->input('parent_id', $data['parent_id']));

        // update and return branch
        return $branch->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Branch $branch
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $data = $branch->toArray();

        $branch->delete();

        return $data;
    }

    /**
     * Reorder branches of tree to create space for new branch.
     *
     * @param \App\Tree $tree
     * @param int       $sorting
     * @param int       $parent_id
     *
     * @return int
     */
    public static function reorder($sorting = null, $parent_id = null, $tree_id = null)
    {
        // filter branches by parent_id and tree_id
        $query = [
            ['parent_id', '=', $parent_id],
            ['tree_id', '=', $tree_id],
        ];

        $branches = Branch::where($query)->get();

        // Log::info(print_r([
        //     'sorting' => $sorting,
        //     'parent_id' => $parent_id,
        //     'tree_id' => $tree_id,
        //     'branches' => $branches->count(),
        // ], true));

        if (!is_numeric($sorting)) {
            // if no sorting is given just append
            $sorting = $branches->count();
        }

        // Log::info(sprintf('sorting %s', $sorting));

        // sort branches by sorting
        $branches = $branches->sortBy(function ($branch) {
            return $branch->sorting;
        });

        // reorder branches
        $index = 0;
        foreach ($branches as $branch) {
            $new_sorting = $index;

            if ($index >= $sorting) {
                $new_sorting = $index + 1;
            }

            $branch->update([
                'sorting' => $new_sorting,
            ]);

            ++$index;
        }

        return $sorting;
    }
}
