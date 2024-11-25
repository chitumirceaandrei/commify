<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('tax_bands')) {
            Schema::create('tax_bands', function (Blueprint $table) {
                $table->id();
                $table->integer('lower_limit');
                $table->integer('upper_limit')->nullable();
                $table->integer('tax_rate');
                $table->timestamps();
            });
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_bands');
    }
};
