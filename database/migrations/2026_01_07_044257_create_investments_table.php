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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')
                ->constrained('investors')
                ->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('investment_date');
            $table->timestamps();

            // One investment per date per investor (as per task requirements)
            $table->unique(['investor_id', 'investment_date']);
            
            $table->index('investment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};