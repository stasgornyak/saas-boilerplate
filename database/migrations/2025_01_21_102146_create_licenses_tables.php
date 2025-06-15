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
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('period');
            $table->decimal('price', 13, 2)->unsigned()->default(0);
            $table->string('currency');
            $table->boolean('is_active')->default(true);
        });

        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained();
            $table
                ->foreignId('tariff_id')
                ->nullable()
                ->constrained();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->string('status');
            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table
                ->string('ext_id')
                ->unique()
                ->nullable();
            $table->decimal('amount', 13, 2)->unsigned();
            $table->string('currency');
            $table->string('status');
            $table->text('description')->nullable();
            $table->foreignId('license_id')->constrained();
            $table->json('details')->nullable();
            $table
                ->foreignId('created_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
            $table->index('created_at');
        });
    }
};
