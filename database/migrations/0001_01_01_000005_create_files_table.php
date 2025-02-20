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
        Schema::create('files', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('name')->nullable();
            $table->text('extension')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_validated')->default(false);
            $table->dateTime('validated_time')->nullable();
            $table->enum('type', ['undefine','coffrage', 'ferraillage', 'divers'])->default('undefine');
            $table->boolean('is_last_index')->default(true);
            $table->integer('distribution_count')->default(0);
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
