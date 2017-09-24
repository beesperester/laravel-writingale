<?php

namespace App\Http\Controllers\v1_0;

// Illuminate
use App\Branch;
use App\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $branches = Branch::allWithRelations();

        return [
            'branches' => $branches,
        ];
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
        // $data = $request->all();

        $update_id = $request->input('update_id');

        $data = [
            'sorting' => $request->input('sorting'),
            'parent_id' => $request->input('parent_id'),
            'tree_id' => $request->input('tree_id'),
        ];

        $trees = collect([]);

        $branches = collect([]);

        // get validator
        $validator = Branch::getValidator($data);

        // validate
        $validator->validate();

        $descendant = null;

        if (isset($update_id)) {
            try {
                $descendant = Branch::findOrFail($update_id);

                // replace sorting with sorting of future descendant
                $data['sorting'] = $descendant->sorting;
            } catch (\Exception $e) {
            }
        } else {
            // reorder branches
            $reorder_result = static::reorder($data['sorting'], $data['parent_id'], $data['tree_id']);

            list($data['sorting'], $branches) = array_values($reorder_result);
        }

        // $data['sorting'] = $sorting;

        // create and return new tree
        $branch = Branch::create($data);

        $branches->push($branch);

        // add affected parent branch
        if (isset($data['parent_id'])) {
            $branches->push(Branch::find($data['parent_id']));
        }

        // add affected tree

        if (isset($data['tree_id'])) {
            $trees->push(Tree::find($data['tree_id']));
        }

        if ($descendant) {
            // update descending branch, if a new ancestor was created
            $descendant->update([
                'parent_id' => $branch->id,
                'tree_id' => null,
                'sorting' => 0,
            ]);

            // add descending branches to update depth property appropriatly
            static::addBranches($descendant, $branches);
        }

        return response()->json([
            'trees' => $trees->map(function ($tree) {
                return $tree->withRelations();
            }),
            'branches' => $branches->map(function ($branch) {
                return $branch->withRelations();
            }),
            'created' => [
                'branch' => $branch
            ]
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
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
        return $branch->withRelations();
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

        $reorder_result = static::reorder(null, $data['parent_id'], $data['tree_id']);

        list($sorting, $branches) = array_values($reorder_result);

        $trees = collect([]);

        // add affected parent tree
        if ($data['tree_id']) {
            $trees->push(Tree::find($data['tree_id']));
        }

        // add affected parent branch
        if ($data['parent_id']) {
            $branches->push(Branch::find($data['parent_id']));
        }

        return response()->json([
            'trees' => $trees->map(function ($tree) {
                return $tree->withRelations();
            }),
            'branches' => $branches->map(function ($branch) {
                return $branch->withRelations();
            }),
            'deleted' => [
                'branch' => $data
            ]
        ])->setEncodingOptions(JSON_NUMERIC_CHECK);
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

        $affected_branches = collect([]);

        // reorder branches
        $index = 0;
        foreach ($branches as $branch) {
            $new_sorting = $index;

            if ($index >= $sorting) {
                $new_sorting = $index + 1;
            }

            // only affect branches whose sorting differs
            if ($branch->sorting != $new_sorting) {
                $branch->update([
                    'sorting' => $new_sorting,
                ]);

                // get fresh branch
                $branch = $branch->fresh();

                Log::info($branch->unique_sorting);

                $affected_branches->push($branch);
            }

            ++$index;
        }

        return [
            'sorting' => $sorting,
            'affected_branches' => $affected_branches,
        ];
    }

    /**
     * Add descending branches to collection.
     *
     * @param \App\Branch                    $branch
     * @param \Illuminate\Support\Collection $branches
     */
    public static function addBranches(Branch $branch, Collection $branches)
    {
        $in_collection = $branches->contains(function ($current_branch) use ($branch) {
            return $current_branch->id == $branch->id;
        });

        if (!$in_collection) {
            $branches->push($branch);
        }

        $branch->branches->map(function ($branch) use ($branches) {
            static::addBranches($branch, $branches);
        });
    }
}
