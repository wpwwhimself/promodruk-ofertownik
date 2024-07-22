<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("label")->nullable();
            $table->text("description")->nullable();
            $table->string("thumbnail_link")->nullable();
            $table->boolean("visible")->default(1);
            $table->integer("ordering")->nullable();
            $table->string("external_link")->nullable();
            $table->foreignId("parent_id")->nullable()->constrained("categories");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
