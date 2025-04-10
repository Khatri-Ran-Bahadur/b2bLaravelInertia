<?php

use App\Enums\TenderStatusEnum;
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
            $table->double('budget', 20, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', array_column(TenderStatusEnum::cases(), 'value'))
                ->default(TenderStatusEnum::Open->value);
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
