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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("desc");
            $table->text("location");
            $table->string('imageSSN');
            $table->string('livePhoto');
            $table->string('nationalId')->unique();
            $table->decimal("min_price");
            $table->enum('status',['available','busy'])->default('available');
            $table->enum('checkByAdmin',['accepted','waiting','rejected'])->default('waiting');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
