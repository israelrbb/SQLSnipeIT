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
        $f2->timestamps = false; //when this model was first created, it had no timestamps. But later on it gets them.
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

        // Modify the process for renaming columns

        // In 'assets' table
        Schema::table('assets', function (Blueprint $table) {
            $table->string('_snipeit_mac_address_new')->nullable(); // create new column
        });

        DB::table('assets')->orderBy('id')->chunk(100, function ($assets) {
            foreach ($assets as $asset) {
                DB::table('assets')
                    ->where('id', $asset->id)
                    ->update(['_snipeit_mac_address_new' => $asset->mac_address]);
            }
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('mac_address');
            $table->renameColumn('_snipeit_mac_address_new', '_snipeit_mac_address');
        });

        // In 'models' table
        Schema::table('models', function (Blueprint $table) {
            $table->boolean('deprecated_mac_address_new')->nullable(); // create new column
        });

        DB::table('models')->orderBy('id')->chunk(100, function ($models) {
            foreach ($models as $model) {
                DB::table('models')
                    ->where('id', $model->id)
                    ->update(['deprecated_mac_address_new' => $model->show_mac_address]);
            }
        });

        Schema::table('models', function (Blueprint $table) {
            $table->dropColumn('show_mac_address');
            $table->renameColumn('deprecated_mac_address_new', 'deprecated_mac_address');
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

        // Modify the process for renaming columns

        // In 'models' table
        Schema::table('models', function (Blueprint $table) {
            $table->boolean('show_mac_address_new')->nullable(); // create new column
        });

        DB::table('models')->orderBy('id')->chunk(100, function ($models) {
            foreach ($models as $model) {
                DB::table('models')
                    ->where('id', $model->id)
                    ->update(['show_mac_address_new' => $model->deprecated_mac_address]);
            }
        });

        Schema::table('models', function (Blueprint $table) {
            $table->dropColumn('deprecated_mac_address');
            $table->renameColumn('show_mac_address_new', 'show_mac_address');
        });

        // In 'assets' table
        if (Schema::hasColumn('assets', '_snipeit_mac_address')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('mac_address_new')->nullable(); // create new column
            });

            DB::table('assets')->orderBy('id')->chunk(100, function ($assets) {
                foreach ($assets as $asset) {
                    DB::table('assets')
                        ->where('id', $asset->id)
                        ->update(['mac_address_new' => $asset->_snipeit_mac_address]);
                }
            });

            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('_snipeit_mac_address');
                $table->renameColumn('mac_address_new', 'mac_address');
            });
        }
    }
}
