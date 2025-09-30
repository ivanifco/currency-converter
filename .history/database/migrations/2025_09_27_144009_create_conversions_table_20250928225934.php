<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations, i.e. create the table.
     */
    public function up() : void
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->string('source_currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('value', 15, 2);
            $table->decimal('converted_value', 15, 2);
            $table->decimal('rate', 15, 6);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations, i.e. delete the table.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};