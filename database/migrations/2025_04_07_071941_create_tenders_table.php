<?php

use App\Enums\TenderStatusEnum;
use App\Enums\TenderActiveStatusEnum;
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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('tender_category_id')->constrained('tender_categories')->onDelete('cascade');
            $table->string('title');
            $table->longText('description');
            $table->string('location')->nullable();
            $table->double('budget_from', 20, 2)->default(0);
            $table->double('budget_to', 20, 2)->default(0);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('status', array_column(TenderStatusEnum::cases(), 'value'))
                ->default(TenderStatusEnum::Open->value);
            $table->enum('active_status', array_column(TenderActiveStatusEnum::cases(), 'value'))
                ->default(TenderActiveStatusEnum::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
