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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->json('settings')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('tenant_user', function (Blueprint $table) {
            $table->foreignId('tenant_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->primary(['tenant_id', 'user_id']);
            $table->boolean('is_owner')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->nullable();
        });
    }
};
