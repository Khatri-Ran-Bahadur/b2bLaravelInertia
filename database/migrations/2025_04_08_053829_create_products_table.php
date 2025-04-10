<?php

use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 2000);
            $table->string('slug', 2000);
            $table->string('brand', 2000)->nullable();

            $table->longText('description')->nullable();
            $table->foreignId('company_id')->index()->constrained('companies');
            $table->foreignId('category_id')->index()->constrained('categories');
            $table->string('status')->index();
            $table->string('quantity')->nullable();
            $table->boolean('is_available')->default(0);
            $table->string('dimention')->nullable();
            $table->string('weight')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('search_keywords')->nullable();
            $table->string('search_keywords_2')->nullable();
            $table->string('material')->nullable();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
