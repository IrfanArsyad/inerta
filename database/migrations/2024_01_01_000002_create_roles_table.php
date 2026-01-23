<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->jsonb('read')->nullable();   // Module IDs or ['*'] for all
            $table->jsonb('create')->nullable(); // Module IDs or ['*'] for all
            $table->jsonb('update')->nullable(); // Module IDs or ['*'] for all
            $table->jsonb('delete')->nullable(); // Module IDs or ['*'] for all
            $table->timestamps();
            $table->softDeletes();
        });

        // Add role_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email')->constrained('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
