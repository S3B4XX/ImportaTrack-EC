<?php

use App\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

/*
 * Se ejecuta en cada arranque del contenedor (ver docker/entrypoint.sh):
 * aplica las migraciones y carga los datos iniciales solo la primera vez,
 * para no sobrescribir lo que el administrador haya cambiado.
 */
Artisan::command('app:bootstrap', function () {
    $this->call('migrate', ['--force' => true]);

    if (Schema::hasTable('roles') && Role::query()->exists()) {
        $this->info('La base ya tiene datos: no se ejecutan los seeders.');

        return;
    }

    $this->call('db:seed', ['--force' => true]);
})->purpose('Migra la base de datos y carga los datos iniciales si está vacía');
