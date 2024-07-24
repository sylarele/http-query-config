<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('bars', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name');
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('bars');
    }
};
