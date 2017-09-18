<?php

namespace App\Http\Controllers\v1_0;

// Illuminate
use App\Tree;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TreeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trees = Tree::all();

        return $trees;
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
        $validator = Tree::getValidator($data);

        // validate
        $this->validator->validate();

        // create and return new tree
        return Tree::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Tree $tree
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Tree $tree)
    {
        return $tree;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Tree $tree
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Tree $tree)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Tree                $tree
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tree $tree)
    {
        // get data from request
        $data = $request->all();

        // get validator
        $validator = Tree::getValidator($data);

        // validate
        $this->validator->validate();

        // update and return tree
        return $tree->update($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Tree $tree
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tree $tree)
    {
        $data = $tree->toArray();

        $tree->delete();

        return $data;
    }
}
