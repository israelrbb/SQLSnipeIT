<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class MigrateMacAddress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $f2 = new \App\Models\CustomFieldset(['name' => 'Asset with MAC Address']);
        $f2->timestamps = false; 
        if (! $f2->save()) {
            throw new Exception("couldn't save customfieldset");
        }
        $macid = DB::table('custom_fields')->insertGetId([
            'name' => 'MAC Address',
            'format' => \App\Models\CustomField::PREDEFINED_FORMATS['MAC'],
            'element'=>'text', ]);
        if (! $macid) {
            throw new Exception("Can't save MAC Custom field: $macid");
        }

        $f2->fields()->attach($macid, ['required' => false, 'order' => 1]);
        \App\Models\AssetModel::where(['show_mac_address' => true])->update(['fieldset_id'=>$f2->id]);

        Schema::table('assets', function (Blueprint $table) {
            $table->string('_snipeit_mac_address')->nullable(); // create new column
        });
        
        // copy data
        DB::table('assets')->orderBy('id')->chunk(100, function ($assets) {
            foreach ($assets as $asset) {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['_snipeit_mac_address' => $asset->mac_address]);
            }
        });
        
        // remove old column
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('mac_address');
        });

        Schema::table('models', function (Blueprint $table) {
            $table->renameColumn('show_mac_address', 'deprecated_mac_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $f = \App\Models\CustomFieldset::where(['name' => 'Asset with MAC Address'])->first();

        if ($f) {
            $f->fields()->delete();
            $f->delete();
        }

        Schema::table('models', function (Blueprint $table) {
            $table->renameColumn('deprecated_mac_address', 'show_mac_address');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->string('mac_address')->nullable(); // create old column
        });

        // copy data
        DB::table('assets')->orderBy('id')->chunk(100, function ($assets) {
            foreach ($assets as $asset) {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['mac_address' => $asset->_snipeit_mac_address]);
            }
        });

        // remove new column
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('_snipeit_mac_address');
        });
    }
}
