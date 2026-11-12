<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_un_visitante_es_enviado_al_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_el_administrador_inicia_sesion_y_ve_el_panel(): void
    {
        $this->post('/login', ['email' => 'admin@importatrack.ec', 'password' => 'ImportaTrack2026'])
            ->assertRedirect('/');

        $this->get('/')->assertRedirect(route('admin.dashboard'));
        $this->get('/admin')->assertOk()->assertSee('Tasas vigentes');
    }

    public function test_el_correo_del_login_no_distingue_mayusculas(): void
    {
        $this->post('/login', ['email' => 'Admin@ImportaTrack.ec', 'password' => 'ImportaTrack2026'])
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }

    public function test_credenciales_incorrectas_muestran_error(): void
    {
        $this->post('/login', ['email' => 'admin@importatrack.ec', 'password' => 'otra'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_un_operador_o_cliente_no_entra_a_la_administracion(): void
    {
        foreach ([Role::OPERATOR, Role::CLIENT] as $role) {
            $user = User::whereRelation('role', 'code', $role)->first();
            $this->actingAs($user)->get('/admin/users')->assertForbidden();
        }
    }

    public function test_todas_las_pantallas_de_la_administracion_cargan(): void
    {
        $admin = User::where('email', 'admin@importatrack.ec')->first();

        foreach ([
            'admin.dashboard', 'admin.users.index', 'admin.users.create', 'admin.suppliers.index',
            'admin.suppliers.create', 'admin.tariff-items.index', 'admin.tariff-items.create',
            'admin.tax-rates.index', 'admin.imports.index',
        ] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }

        $this->actingAs($admin)->get(route('admin.imports.show', 1))->assertOk()->assertSee('$13.649,78');
    }
}
