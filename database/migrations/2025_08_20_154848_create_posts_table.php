<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('제목');
            $table->longText('content')->comment('내용');
            $table->string('write', 50)->comment('작성자');
            $table->string('password')->comment('패스워드');
            $table->timestamps();
            $table->softDeletes()->comment('소프트 삭제용');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
