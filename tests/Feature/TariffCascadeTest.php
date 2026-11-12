<?php

namespace Tests\Feature;

use App\Models\TariffChapter;
use App\Models\TariffSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Dropdown en cascada Sección → Capítulo y sus validaciones en el servidor. */
class TariffCascadeTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('email', 'admin@importatrack.ec')->first();
    }

    public function test_el_endpoint_devuelve_solo_los_capitulos_de_la_seccion(): void
    {
        $section = TariffSection::where('code', 'XVI')->first();

        $this->actingAs($this->admin())
            ->getJson(route('admin.tariff-sections.chapters', $section))
            ->assertOk()
            ->assertJsonCount(2)
            ->assertJsonPath('0.code', '84')
            ->assertJsonPath('1.code', '85');
    }

    public function test_crea_una_partida_y_guarda_el_codigo_sin_puntos(): void
    {
        $chapter = TariffChapter::where('code', '85')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.tariff-items.store'), [
                'tariff_section_id' => $chapter->tariff_section_id,
                'tariff_chapter_id' => $chapter->id,
                'code' => '8542.31.00.00',
                'description' => 'Procesadores y controladores',
                'ad_valorem_rate' => '7.5',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('tariff_items', ['code' => '8542310000', 'tariff_chapter_id' => $chapter->id]);
    }

    public function test_rechaza_un_capitulo_que_no_pertenece_a_la_seccion(): void
    {
        $otherSection = TariffSection::where('code', 'I')->first();
        $chapter = TariffChapter::where('code', '85')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.tariff-items.store'), [
                'tariff_section_id' => $otherSection->id,
                'tariff_chapter_id' => $chapter->id,
                'code' => '8542310000',
                'description' => 'X',
                'ad_valorem_rate' => 5,
            ])
            ->assertSessionHasErrors(['tariff_chapter_id' => 'El capítulo seleccionado no pertenece a la sección elegida.']);
    }

    public function test_el_codigo_debe_empezar_con_el_capitulo(): void
    {
        $chapter = TariffChapter::where('code', '85')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.tariff-items.store'), [
                'tariff_section_id' => $chapter->tariff_section_id,
                'tariff_chapter_id' => $chapter->id,
                'code' => '8471800000',
                'description' => 'X',
                'ad_valorem_rate' => 5,
            ])
            ->assertSessionHasErrors(['code' => 'El código debe empezar con 85, que es el capítulo seleccionado.']);
    }

    public function test_el_ad_valorem_debe_estar_entre_0_y_100(): void
    {
        $chapter = TariffChapter::where('code', '85')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.tariff-items.store'), [
                'tariff_section_id' => $chapter->tariff_section_id,
                'tariff_chapter_id' => $chapter->id,
                'code' => '8542310000',
                'description' => 'X',
                'ad_valorem_rate' => 150,
            ])
            ->assertSessionHasErrors('ad_valorem_rate');
    }

    public function test_un_codigo_enviado_como_arreglo_es_un_error_de_validacion(): void
    {
        $chapter = TariffChapter::where('code', '85')->first();

        $this->actingAs($this->admin())
            ->post(route('admin.tariff-items.store'), [
                'tariff_section_id' => $chapter->tariff_section_id,
                'tariff_chapter_id' => $chapter->id,
                'code' => ['8542310000'],
                'description' => 'X',
                'ad_valorem_rate' => 5,
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('code');
    }
}
