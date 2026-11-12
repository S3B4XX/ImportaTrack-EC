<?php

namespace Database\Seeders;

use App\Enums\CustomsChannel;
use App\Enums\ImportStatus;
use App\Enums\TransportMode;
use App\Models\Import;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\TariffItem;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos iniciales. Es idempotente (updateOrCreate): se puede ejecutar en cada deploy.
 * Contraseña de todos los usuarios de demostración: ImportaTrack2026
 */
class DatabaseSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'ImportaTrack2026';

    public function run(): void
    {
        $this->seedRoles();
        $this->seedUsers();
        $this->seedTaxRates();
        $this->call(TariffSeeder::class);
        $this->seedSuppliers();
        $this->seedImports();
    }

    private function seedRoles(): void
    {
        $roles = [
            Role::ADMIN => ['Administrador', 'Gestiona usuarios, catálogos y tasas; consulta todas las importaciones.'],
            Role::OPERATOR => ['Operador', 'Registra las importaciones y sus eventos de seguimiento.'],
            Role::CLIENT => ['Cliente', 'Importador: consulta sus importaciones, liquidaciones y el simulador.'],
        ];

        foreach ($roles as $code => [$name, $description]) {
            Role::updateOrCreate(['code' => $code], compact('name', 'description'));
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['admin@importatrack.ec', Role::ADMIN, 'Andrea', 'Díaz', '0991234567', null, null],
            ['operador@importatrack.ec', Role::OPERATOR, 'Carlos', 'Mena', '0987654321', null, '1704567898'],
            ['compras@importadoraandina.ec', Role::CLIENT, 'María', 'Torres', '022345678', 'Importadora Andina S.A.', '1792345677001'],
            ['logistica@tecnoparts.ec', Role::CLIENT, 'Luis', 'Vera', '042876543', 'TecnoParts Cía. Ltda.', '0991876545001'],
            ['jorge.salazar@gmail.com', Role::CLIENT, 'Jorge', 'Salazar', '0998877665', 'Jorge Salazar (persona natural)', '1712345675001'],
        ];

        foreach ($users as [$email, $role, $first, $last, $phone, $company, $ruc]) {
            User::updateOrCreate(['email' => $email], [
                'role_id' => Role::idFor($role),
                'first_name' => $first,
                'last_name' => $last,
                'phone' => $phone,
                'company_name' => $company,
                'ruc' => $ruc,
                'password' => self::DEMO_PASSWORD,
            ]);
        }
    }

    private function seedTaxRates(): void
    {
        TaxRate::updateOrCreate(['code' => TaxRate::IVA], ['name' => 'Impuesto al Valor Agregado', 'rate' => 15, 'valid_from' => '2024-04-01']);
        TaxRate::updateOrCreate(['code' => TaxRate::FODINFA], ['name' => 'Fondo de Desarrollo para la Infancia', 'rate' => 0.5, 'valid_from' => '2024-01-01']);
    }

    private function seedSuppliers(): void
    {
        $suppliers = [
            ['Shenzhen Server Co.', 'China', 'sales@szserver.cn', '+86 755 8888 1234'],
            ['Miami IT Supply', 'Estados Unidos', 'orders@miamiitsupply.com', '+1 305 555 0142'],
            ['Hamburg Storage GmbH', 'Alemania', 'vertrieb@hamburg-storage.de', '+49 40 1234 5678'],
            ['Panamá Free Zone Traders', 'Panamá', 'ventas@pfztraders.com.pa', '+507 433 2211'],
        ];

        foreach ($suppliers as [$name, $country, $email, $phone]) {
            Supplier::updateOrCreate(['name' => $name], compact('country', 'email', 'phone'));
        }
    }

    private function seedImports(): void
    {
        $operator = User::where('email', 'operador@importatrack.ec')->first();
        $andina = User::where('email', 'compras@importadoraandina.ec')->first();
        $tecno = User::where('email', 'logistica@tecnoparts.ec')->first();
        $supplier = fn (string $name) => Supplier::where('name', $name)->value('id');
        $tariff = fn (string $code) => TariffItem::where('code', $code)->value('id');

        $imports = [
            // Ejemplo de la especificación: costo total $13.649,78 y desviación 13,3% (Retraso leve).
            [
                'code' => 'IMP-2026-0001', 'client' => $andina, 'supplier' => 'Shenzhen Server Co.',
                'mode' => TransportMode::Maritimo, 'incoterm' => 'FOB', 'freight' => 800, 'insurance' => 100, 'local' => 300,
                'status' => ImportStatus::Liberada, 'channel' => CustomsChannel::Documental,
                'shipped' => '2026-07-01', 'estimated' => '2026-07-31', 'released' => '2026-08-04', 'delivered' => null,
                'items' => [['8471500000', 'Servidores', 10, 800], ['8471700000', 'Discos duros', 50, 40]],
                'events' => [
                    [ImportStatus::Registrada, '2026-06-25 10:00', 'Quito', 'Importación creada por la agencia.'],
                    [ImportStatus::Embarcada, '2026-07-01 08:30', 'Puerto de Shenzhen', 'Contenedor 20 pies embarcado.'],
                    [ImportStatus::EnTransito, '2026-07-02 12:00', 'Océano Pacífico', null],
                    [ImportStatus::Arribo, '2026-07-27 06:00', 'Puerto de Guayaquil', null],
                    [ImportStatus::EnAforo, '2026-07-28 09:15', 'SENAE Guayaquil', 'Canal documental.'],
                    [ImportStatus::Liberada, '2026-08-04 16:40', 'SENAE Guayaquil', 'Carga autorizada.'],
                ],
            ],
            [
                'code' => 'IMP-2026-0002', 'client' => $tecno, 'supplier' => 'Miami IT Supply',
                'mode' => TransportMode::Aereo, 'incoterm' => 'EXW', 'freight' => 450, 'insurance' => 60, 'local' => 180,
                'status' => ImportStatus::Entregada, 'channel' => CustomsChannel::Automatico,
                'shipped' => '2026-08-10', 'estimated' => '2026-08-17', 'released' => '2026-08-16', 'delivered' => '2026-08-18',
                'items' => [['8517620000', 'Switches de 48 puertos', 4, 1250], ['8544429000', 'Cables de red Cat6', 200, 3.5]],
                'events' => [
                    [ImportStatus::Registrada, '2026-08-08 11:00', 'Guayaquil', null],
                    [ImportStatus::Embarcada, '2026-08-10 22:00', 'Aeropuerto de Miami', null],
                    [ImportStatus::Arribo, '2026-08-11 05:30', 'Aeropuerto de Quito', null],
                    [ImportStatus::EnAforo, '2026-08-12 10:00', 'SENAE Quito', 'Canal automático.'],
                    [ImportStatus::Liberada, '2026-08-16 15:00', 'SENAE Quito', null],
                    [ImportStatus::Entregada, '2026-08-18 09:00', 'Bodega del cliente', null],
                ],
            ],
            [
                'code' => 'IMP-2026-0003', 'client' => $andina, 'supplier' => 'Hamburg Storage GmbH',
                'mode' => TransportMode::Maritimo, 'incoterm' => 'CIF', 'freight' => 1200, 'insurance' => 150, 'local' => 400,
                'status' => ImportStatus::EnTransito, 'channel' => null,
                'shipped' => '2026-10-20', 'estimated' => '2026-11-30', 'released' => null, 'delivered' => null,
                'items' => [['8523510000', 'SSD empresariales 3.84 TB', 20, 410], ['9403200000', 'Racks de 42U', 2, 900]],
                'events' => [
                    [ImportStatus::Registrada, '2026-10-15 09:00', 'Quito', null],
                    [ImportStatus::Embarcada, '2026-10-20 14:00', 'Puerto de Hamburgo', null],
                    [ImportStatus::EnTransito, '2026-10-21 08:00', 'Océano Atlántico', 'Transbordo previsto en Cartagena.'],
                ],
            ],
        ];

        foreach ($imports as $data) {
            $import = Import::updateOrCreate(['code' => $data['code']], [
                'client_id' => $data['client']->id,
                'operator_id' => $operator->id,
                'supplier_id' => $supplier($data['supplier']),
                'transport_mode' => $data['mode'],
                'incoterm' => $data['incoterm'],
                'freight' => $data['freight'],
                'insurance' => $data['insurance'],
                'local_costs' => $data['local'],
                'status' => $data['status'],
                'customs_channel' => $data['channel'],
                'shipped_at' => $data['shipped'],
                'estimated_release_at' => $data['estimated'],
                'released_at' => $data['released'],
                'delivered_at' => $data['delivered'],
            ]);

            $import->items()->delete();
            foreach ($data['items'] as [$code, $description, $quantity, $unitFob]) {
                $import->items()->create([
                    'tariff_item_id' => $tariff($code),
                    'description' => $description,
                    'quantity' => $quantity,
                    'unit_fob' => $unitFob,
                ]);
            }

            $import->events()->delete();
            foreach ($data['events'] as [$status, $date, $location, $note]) {
                $import->events()->create([
                    'user_id' => $operator->id,
                    'status' => $status,
                    'event_date' => $date,
                    'location' => $location,
                    'note' => $note,
                ]);
            }
        }
    }
}
