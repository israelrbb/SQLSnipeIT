<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameComponentTotalQtyToQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->integer('qty_new')->nullable(); // create new column
        });

        // copy data
        DB::table('components')->orderBy('id')->chunk(100, function ($components) {
            foreach ($components as $component) {
                DB::table('components')
                    ->where('id', $component->id)
                    ->update(['qty_new' => $component->total_qty]);
            }
        });

        // remove old column
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('total_qty');
        });

        // rename new column to old column name
        Schema::table('components', function (Blueprint $table) {
            $table->renameColumn('qty_new', 'qty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add back the old column with its original type
        Schema::table('components', function (Blueprint $table) {
            $table->integer('total_qty_new')->nullable();
        });

        // Copy data from new to old
        DB::table('components')->orderBy('id')->chunk(100, function ($components) {
            foreach ($components as $component) {
                DB::table('components')
                    ->where('id', $component->id)
                    ->update(['total_qty_new' => $component->qty]);
            }
        });

        // remove the new column
        Schema::table('components', function (Blueprint $table) {
            $table->dropColumn('qty');
        });

        // rename old column to its original name
        Schema::table('components', function (Blueprint $table) {
            $table->renameColumn('total_qty_new', 'total_qty');
        });
    }
}
