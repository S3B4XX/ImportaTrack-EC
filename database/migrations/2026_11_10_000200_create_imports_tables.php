<?php

use App\Enums\CustomsChannel;
use App\Enums\ImportStatus;
use App\Enums\TransportMode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas del Core: importaciones, sus productos y los eventos de seguimiento.
 *
 * Los ENUM del diagrama ER se crean con enum() de Laravel: en PostgreSQL y en SQLite
 * queda una columna de texto con una restricción CHECK que solo acepta los valores
 * de la lista. Así la migración es la misma en los dos motores, sin SQL propio de ninguno.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();     // IMP-2026-0001
            $table->foreignId('client_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->enum('transport_mode', TransportMode::values());
            $table->string('incoterm', 3);
            $table->decimal('freight', 12, 2)->default(0);
            $table->decimal('insurance', 12, 2)->default(0);
            $table->decimal('local_costs', 12, 2)->default(0);
            $table->enum('status', ImportStatus::values())->default(ImportStatus::Registrada->value);
            $table->enum('customs_channel', CustomsChannel::values())->nullable();
            $table->date('shipped_at')->nullable();
            $table->date('estimated_release_at')->nullable();
            $table->date('released_at')->nullable();
            $table->date('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('import_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->cascadeOnDelete();
            $table->foreignId('tariff_item_id')->constrained('tariff_items')->restrictOnDelete();
            $table->string('description', 255);
            $table->integer('quantity');
            $table->decimal('unit_fob', 12, 2);
            $table->timestamps();
        });

        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_id')->constrained('imports')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', ImportStatus::values());
            $table->timestamp('event_date');
            $table->string('location', 120)->nullable();
            $table->string('note', 300)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
        Schema::dropIfExists('import_items');
        Schema::dropIfExists('imports');
    }
};
