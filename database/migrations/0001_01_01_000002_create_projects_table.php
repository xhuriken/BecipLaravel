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
        Schema::create('projects', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('namelong')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('referent_id')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_mask_valided')->default(false);
            $table->boolean('is_mask_distributed')->default(false);
            $table->text('comment')->nullable()->default(null);
            $table->timestamps();

            // Foreign
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('referent_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
