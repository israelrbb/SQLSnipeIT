<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateLdapFilterToLongerField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('ldap_filter_new')->nullable(); // create new column
        });

        // copy data
        DB::table('settings')->orderBy('id')->chunk(100, function ($settings) {
            foreach ($settings as $setting) {
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['ldap_filter_new' => $setting->ldap_filter]);
            }
        });

        // remove old column
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('ldap_filter');
        });

        // rename new column to old column name
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('ldap_filter_new', 'ldap_filter');
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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('ldap_filter_old')->nullable();
        });

        // Copy data from new to old
        DB::table('settings')->orderBy('id')->chunk(100, function ($settings) {
            foreach ($settings as $setting) {
                DB::table('settings')
                    ->where('id', $setting->id)
                    ->update(['ldap_filter_old' => $setting->ldap_filter]);
            }
        });

        // remove the new column
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('ldap_filter');
        });

        // rename old column to its original name
        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('ldap_filter_old', 'ldap_filter');
        });
    }
}
