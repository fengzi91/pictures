<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('password')->nullable()->comment('密码');
            $table->string('title')->nullable()->comment('标题');
            $table->uuid('link')->unique()->comment('链接');
            $table->json('counts')->nullable()->comment('统计字段');
            $table->timestamps();
        });
        Schema::create('collect_picture', function (Blueprint $table) {
            $table->foreignId('collect_id')->constrained()->onDelete('cascade');
            $table->foreignId('picture_id')->constrained()->onDelete('cascade');
            $table->unique(['collect_id', 'picture_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collect_picture', function(Blueprint $table) {
            $table->dropForeign(['collect_id']);
            $table->dropForeign(['picture_id']);
        });
        Schema::dropIfExists('collects');
        Schema::dropIfExists('collect_picture');
    }
}
