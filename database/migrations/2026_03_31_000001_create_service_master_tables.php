<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('service_master')) {
            Schema::create('service_master', function (Blueprint $table) {
                $table->increments('ID');
                $table->string('ServiceName', 100);
                $table->string('Description', 100)->nullable();
                $table->string('Status', 100);
                $table->string('Created_by', 100)->default('');
                $table->timestamp('Created_on')->useCurrent();
                $table->string('updated_by', 100)->default('');
                $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate();
            });
        }

        if (! Schema::hasTable('service_master_items')) {
            Schema::create('service_master_items', function (Blueprint $table) {
                $table->unsignedInteger('service_id');
                $table->unsignedInteger('item_id');

                $table->primary(['service_id', 'item_id']);
                $table->index('service_id');
                $table->index('item_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_master_items');
        Schema::dropIfExists('service_master');
    }
};
