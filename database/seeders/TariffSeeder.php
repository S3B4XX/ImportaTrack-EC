<?php

namespace Database\Seeders;

use App\Models\TariffChapter;
use App\Models\TariffItem;
use App\Models\TariffSection;
use Illuminate\Database\Seeder;

/**
 * Estructura del Arancel del Ecuador (Sistema Armonizado): 21 secciones y sus capítulos,
 * más un conjunto inicial de subpartidas (la especificación indica cargarlas con un seeder).
 * Los porcentajes Ad Valorem son de ejemplo, como en el documento de especificación.
 */
class TariffSeeder extends Seeder
{
    private const SECTIONS = [
        'I' => ['Animales vivos y productos del reino animal', ['01' => 'Animales vivos', '02' => 'Carne y despojos comestibles', '03' => 'Pescados y crustáceos, moluscos y demás invertebrados acuáticos', '04' => 'Leche y productos lácteos; huevos de ave; miel natural', '05' => 'Los demás productos de origen animal']],
        'II' => ['Productos del reino vegetal', ['06' => 'Plantas vivas y productos de la floricultura', '07' => 'Hortalizas, plantas, raíces y tubérculos alimenticios', '08' => 'Frutas y frutos comestibles', '09' => 'Café, té, yerba mate y especias', '10' => 'Cereales', '11' => 'Productos de la molinería; malta; almidón', '12' => 'Semillas y frutos oleaginosos; plantas industriales o medicinales', '13' => 'Gomas, resinas y demás jugos y extractos vegetales', '14' => 'Materias trenzables y demás productos de origen vegetal']],
        'III' => ['Grasas y aceites animales, vegetales o de origen microbiano', ['15' => 'Grasas y aceites animales, vegetales o de origen microbiano']],
        'IV' => ['Productos de las industrias alimentarias; bebidas y tabaco', ['16' => 'Preparaciones de carne, pescado o crustáceos', '17' => 'Azúcares y artículos de confitería', '18' => 'Cacao y sus preparaciones', '19' => 'Preparaciones a base de cereales, harina, almidón o leche', '20' => 'Preparaciones de hortalizas, frutas u otros frutos', '21' => 'Preparaciones alimenticias diversas', '22' => 'Bebidas, líquidos alcohólicos y vinagre', '23' => 'Residuos de las industrias alimentarias; alimentos para animales', '24' => 'Tabaco y sucedáneos del tabaco elaborados']],
        'V' => ['Productos minerales', ['25' => 'Sal; azufre; tierras y piedras; yesos, cales y cementos', '26' => 'Minerales metalíferos, escorias y cenizas', '27' => 'Combustibles minerales, aceites minerales y productos de su destilación']],
        'VI' => ['Productos de las industrias químicas o de las industrias conexas', ['28' => 'Productos químicos inorgánicos', '29' => 'Productos químicos orgánicos', '30' => 'Productos farmacéuticos', '31' => 'Abonos', '32' => 'Extractos curtientes o tintóreos; pinturas y barnices; tintas', '33' => 'Aceites esenciales; preparaciones de perfumería o de cosmética', '34' => 'Jabón, preparaciones para lavar, ceras y velas', '35' => 'Materias albuminoideas; colas; enzimas', '36' => 'Pólvora y explosivos; artículos de pirotecnia; fósforos', '37' => 'Productos fotográficos o cinematográficos', '38' => 'Productos diversos de las industrias químicas']],
        'VII' => ['Plástico y caucho y sus manufacturas', ['39' => 'Plástico y sus manufacturas', '40' => 'Caucho y sus manufacturas']],
        'VIII' => ['Pieles, cueros, peletería y sus manufacturas', ['41' => 'Pieles (excepto la peletería) y cueros', '42' => 'Manufacturas de cuero; artículos de viaje, bolsos de mano', '43' => 'Peletería y confecciones de peletería']],
        'IX' => ['Madera, corcho, cestería y sus manufacturas', ['44' => 'Madera, carbón vegetal y manufacturas de madera', '45' => 'Corcho y sus manufacturas', '46' => 'Manufacturas de espartería o cestería']],
        'X' => ['Pasta de madera, papel o cartón y sus aplicaciones', ['47' => 'Pasta de madera o de las demás materias fibrosas celulósicas', '48' => 'Papel y cartón; manufacturas de pasta de celulosa, papel o cartón', '49' => 'Productos editoriales, de la prensa y de las demás industrias gráficas']],
        'XI' => ['Materias textiles y sus manufacturas', ['50' => 'Seda', '51' => 'Lana y pelo fino u ordinario', '52' => 'Algodón', '53' => 'Las demás fibras textiles vegetales', '54' => 'Filamentos sintéticos o artificiales', '55' => 'Fibras sintéticas o artificiales discontinuas', '56' => 'Guata, fieltro y tela sin tejer; cordeles y cuerdas', '57' => 'Alfombras y demás revestimientos para el suelo de materia textil', '58' => 'Tejidos especiales; encajes; tapicería; bordados', '59' => 'Telas impregnadas, recubiertas o estratificadas', '60' => 'Tejidos de punto', '61' => 'Prendas y complementos de vestir, de punto', '62' => 'Prendas y complementos de vestir, excepto los de punto', '63' => 'Los demás artículos textiles confeccionados; prendería']],
        'XII' => ['Calzado, sombreros, paraguas y artículos similares', ['64' => 'Calzado, polainas y artículos análogos', '65' => 'Sombreros, demás tocados y sus partes', '66' => 'Paraguas, sombrillas, bastones y látigos', '67' => 'Plumas y plumón preparados; flores artificiales']],
        'XIII' => ['Manufacturas de piedra, cerámica y vidrio', ['68' => 'Manufacturas de piedra, yeso, cemento, amianto o mica', '69' => 'Productos cerámicos', '70' => 'Vidrio y sus manufacturas']],
        'XIV' => ['Perlas, piedras y metales preciosos; bisutería; monedas', ['71' => 'Perlas, piedras preciosas, metales preciosos y bisutería']],
        'XV' => ['Metales comunes y manufacturas de estos metales', ['72' => 'Fundición, hierro y acero', '73' => 'Manufacturas de fundición, hierro o acero', '74' => 'Cobre y sus manufacturas', '75' => 'Níquel y sus manufacturas', '76' => 'Aluminio y sus manufacturas', '78' => 'Plomo y sus manufacturas', '79' => 'Cinc y sus manufacturas', '80' => 'Estaño y sus manufacturas', '81' => 'Los demás metales comunes; cermets', '82' => 'Herramientas y útiles, artículos de cuchillería de metal común', '83' => 'Manufacturas diversas de metal común']],
        'XVI' => ['Máquinas y aparatos, material eléctrico y sus partes', ['84' => 'Reactores nucleares, calderas, máquinas, aparatos y artefactos mecánicos', '85' => 'Máquinas, aparatos y material eléctrico; aparatos de grabación o reproducción de sonido e imagen']],
        'XVII' => ['Material de transporte', ['86' => 'Vehículos y material para vías férreas', '87' => 'Vehículos automóviles, tractores y demás vehículos terrestres', '88' => 'Aeronaves, vehículos espaciales y sus partes', '89' => 'Barcos y demás artefactos flotantes']],
        'XVIII' => ['Instrumentos de óptica, fotografía, medida y médicos; relojería; instrumentos musicales', ['90' => 'Instrumentos y aparatos de óptica, fotografía, medida, control o médicos', '91' => 'Aparatos de relojería y sus partes', '92' => 'Instrumentos musicales; sus partes y accesorios']],
        'XIX' => ['Armas, municiones, y sus partes y accesorios', ['93' => 'Armas, municiones, y sus partes y accesorios']],
        'XX' => ['Mercancías y productos diversos', ['94' => 'Muebles; mobiliario médico-quirúrgico; artículos de cama; luminarias', '95' => 'Juguetes, juegos y artículos para recreo o deporte', '96' => 'Manufacturas diversas']],
        'XXI' => ['Objetos de arte o colección y antigüedades', ['97' => 'Objetos de arte o colección y antigüedades']],
    ];

    /**
     * Subpartidas iniciales: código (10 dígitos) => [descripción, % Ad Valorem].
     * Hay al menos una por cada sección, para que el filtro y la cascada tengan datos en todas.
     */
    private const ITEMS = [
        // I — Animales vivos y productos del reino animal
        '0207140000' => ['Trozos y despojos de gallo o gallina, congelados', 25],
        '0402210000' => ['Leche en polvo entera, sin adición de azúcar', 25],
        // II — Productos del reino vegetal
        '0901210000' => ['Café tostado sin descafeinar', 30],
        '1001990000' => ['Los demás trigos y morcajo (trigo para panificación)', 0],
        // III — Grasas y aceites
        '1511900000' => ['Aceite de palma refinado y sus fracciones', 20],
        // IV — Industrias alimentarias, bebidas y tabaco
        '1704901000' => ['Bombones, caramelos, confites y pastillas sin cacao', 20],
        '2204210000' => ['Vino de uvas frescas en recipientes de hasta 2 litros', 25],
        // V — Productos minerales
        '2523290000' => ['Cemento Portland, excepto el blanco', 15],
        // VI — Industrias químicas
        '3004902900' => ['Los demás medicamentos para uso humano, dosificados', 0],
        '3304990000' => ['Las demás preparaciones de belleza, maquillaje y cuidado de la piel', 20],
        // VII — Plástico y caucho
        '3926909000' => ['Las demás manufacturas de plástico', 15],
        '4011100000' => ['Neumáticos nuevos de caucho para automóviles de turismo', 10],
        // VIII — Pieles y cueros
        '4202920000' => ['Bolsos, mochilas y estuches con superficie exterior de plástico o materia textil', 20],
        // IX — Madera y sus manufacturas
        '4421999000' => ['Las demás manufacturas de madera', 20],
        // X — Papel y cartón
        '4820200000' => ['Cuadernos de papel o cartón', 20],
        // XI — Materias textiles
        '6109100000' => ['Camisetas (T-shirts) de punto, de algodón', 30],
        // XII — Calzado y sombreros
        '6404110000' => ['Calzado de deporte con parte superior de materia textil', 30],
        // XIII — Piedra, cerámica y vidrio
        '6907210000' => ['Baldosas de cerámica (porcelanato) con absorción de agua ≤ 0,5%', 25],
        // XIV — Perlas, metales preciosos y bisutería
        '7117190000' => ['Bisutería de metal común', 25],
        // XV — Metales comunes
        '7308900000' => ['Las demás construcciones y partes de estructuras de hierro o acero', 15],
        '8205590000' => ['Las demás herramientas de mano', 10],
        // XVI — Máquinas y material eléctrico
        '8471500000' => ['Unidades de proceso (servidores), excepto las de las subpartidas 8471.41 u 8471.49', 5],
        '8471700000' => ['Unidades de memoria (discos duros)', 10],
        '8473300000' => ['Partes y accesorios de máquinas de la partida 84.71', 5],
        '8504401000' => ['Fuentes de alimentación estabilizada (convertidores estáticos)', 10],
        '8517620000' => ['Aparatos para la recepción, conversión y transmisión de datos (switches, routers)', 5],
        '8523510000' => ['Dispositivos de almacenamiento permanente de datos a base de semiconductores (SSD)', 10],
        '8544429000' => ['Los demás conductores eléctricos provistos de piezas de conexión (cables)', 15],
        // XVII — Material de transporte
        '8708300000' => ['Frenos y servofrenos y sus partes, para vehículos automóviles', 15],
        // XVIII — Óptica, instrumentos médicos y relojería
        '9018390000' => ['Agujas, catéteres, cánulas e instrumentos similares de uso médico', 5],
        '9102110000' => ['Relojes de pulsera eléctricos con indicador mecánico', 15],
        // XIX — Armas y municiones
        '9304000000' => ['Las demás armas (de aire comprimido, de resorte o de gas)', 30],
        // XX — Mercancías diversas
        '9403200000' => ['Los demás muebles de metal (racks y gabinetes)', 20],
        '9503009900' => ['Los demás juguetes', 20],
        '9506910000' => ['Artículos y material para cultura física y gimnasia', 15],
        // XXI — Objetos de arte y antigüedades
        '9703900000' => ['Las demás obras originales de estatuaria o escultura', 0],
    ];

    public function run(): void
    {
        foreach (self::SECTIONS as $sectionCode => [$sectionName, $chapters]) {
            $section = TariffSection::updateOrCreate(['code' => $sectionCode], ['name' => $sectionName]);

            foreach ($chapters as $chapterCode => $chapterName) {
                TariffChapter::updateOrCreate(
                    ['code' => $chapterCode],
                    ['tariff_section_id' => $section->id, 'name' => $chapterName],
                );
            }
        }

        foreach (self::ITEMS as $code => [$description, $rate]) {
            TariffItem::updateOrCreate(['code' => $code], [
                'tariff_chapter_id' => TariffChapter::where('code', substr($code, 0, 2))->value('id'),
                'description' => $description,
                'ad_valorem_rate' => $rate,
            ]);
        }
    }
}
