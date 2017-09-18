<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            // connect with parent tree
            $table->unsignedInteger('tree_id')->nullable();
            $table->foreign('tree_id')->references('id')->on('trees')->onDelete('cascade');

            // connect with possible parent branch
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('branches')->onDelete('cascade');

            // for sorting branches
            $table->unsignedInteger('sorting')->nullable();

            // branch content
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
