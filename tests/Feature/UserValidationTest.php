<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * El RUC/cédula es el dato sensible: se valida en el back-end aunque la petición
 * llegue sin pasar por el formulario (como haría Postman o un JS desactivado).
 */
class UserValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('email', 'admin@importatrack.ec')->first();
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'role_id' => Role::idFor(Role::CLIENT),
            'first_name' => 'Paola',
            'last_name' => 'Ruiz',
            'email' => 'paola@demo.ec',
            'company_name' => 'Demo Importaciones S.A.',
            'ruc' => '1798765430001',
            'password' => 'Clave2026',
            'password_confirmation' => 'Clave2026',
        ], $overrides);
    }

    public function test_crea_un_cliente_con_ruc_valido(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload())
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'paola@demo.ec', 'ruc' => '1798765430001']);
    }

    public function test_un_ruc_con_digito_verificador_incorrecto_no_se_guarda(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload(['ruc' => '1798765431001']))
            ->assertSessionHasErrors(['ruc' => 'El RUC/cédula no es válido: el dígito verificador no coincide.']);

        $this->assertDatabaseMissing('users', ['email' => 'paola@demo.ec']);
    }

    public function test_un_cliente_debe_tener_ruc_de_13_digitos(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload(['ruc' => '1710034065']))
            ->assertSessionHasErrors('ruc');

        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload(['ruc' => '']))
            ->assertSessionHasErrors('ruc');
    }

    public function test_un_operador_puede_registrarse_con_cedula(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload([
                'role_id' => Role::idFor(Role::OPERATOR),
                'ruc' => '1710034065',
                'company_name' => null,
            ]))
            ->assertSessionHasNoErrors();
    }

    public function test_no_se_repite_un_ruc(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload(['ruc' => '1792345677001']))
            ->assertSessionHasErrors(['ruc' => 'Ya existe un usuario registrado con este RUC/cédula.']);
    }

    public function test_no_se_elimina_un_cliente_con_importaciones(): void
    {
        $client = User::where('email', 'compras@importadoraandina.ec')->first();

        $this->actingAs($this->admin())
            ->delete(route('admin.users.destroy', $client))
            ->assertSessionHas('error');

        $this->assertModelExists($client);
    }

    public function test_un_ruc_enviado_como_arreglo_es_un_error_de_validacion(): void
    {
        // Petición manipulada (ruc[]=...): debe volver con error en el campo, no con un error 500.
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->payload(['ruc' => ['1798765430001']]))
            ->assertRedirect()
            ->assertSessionHasErrors('ruc');

        $this->assertDatabaseMissing('users', ['email' => 'paola@demo.ec']);
    }

    public function test_un_administrador_no_puede_cambiar_su_propio_rol(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'role_id' => Role::idFor(Role::OPERATOR),
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'email' => $admin->email,
            ])
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->isAdmin());
    }
}
