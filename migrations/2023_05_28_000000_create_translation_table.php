<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();

            $table->string('key', 100)->charset('ascii')->collation('ascii_bin');
            $table->string('namespace', 100)->charset('ascii')->collation('ascii_bin')->default('*');
            $table->text('value')->nullable();

            $table->string('language_iso', 5)->charset('ascii')->collation('ascii_bin');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['language_iso', 'deleted_at']);
            $table->index(['key', 'language_iso', 'deleted_at']);
            $table->index(['key', 'namespace', 'language_iso', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::drop('translations');
    }
};
