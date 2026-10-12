<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::create([
            'name' => 'Owner SyfaBuild', 'username' => 'owner', 'email' => 'owner@syfabuild.test',
            'password' => 'OwnerSyfa2026!', 'role' => 'owner',
        ]);
        User::create([
            'name' => 'Gudang Demo', 'username' => 'gudang1', 'email' => 'gudang@syfabuild.test',
            'password' => 'GudangSyfa2026!', 'role' => 'gudang',
        ]);
        User::create([
            'name' => 'Kasir Demo', 'username' => 'kasir1', 'email' => 'kasir@syfabuild.test',
            'password' => 'KasirSyfa2026!', 'role' => 'kasir',
        ]);

        $suppliers = collect([
            ['Toko Sumber Jaya', '081234567801', 'Jl. Merdeka No. 10, Bireuen'],
            ['CV Bangun Makmur', '081234567802', 'Jl. Sudirman No. 25, Lhokseumawe'],
            ['UD Sentosa Abadi', '081234567803', 'Jl. Diponegoro No. 7, Bireuen'],
        ])->map(fn ($s) => Supplier::create(['name' => $s[0], 'phone' => $s[1], 'address' => $s[2]]));

        // Nama kategori => [nama produk, deskripsi, satuan, harga modal, harga jual]
        $catalog = [
            'Semen' => [
                ['Semen Tiga Roda 50 kg', 'Semen portland untuk pondasi, cor, dan plesteran', 'Sak', 58000, 65000],
                ['Semen Gresik 40 kg', 'Semen serbaguna untuk pekerjaan rumah', 'Sak', 48000, 54000],
                ['Semen Putih 5 kg', 'Untuk finishing dan nat keramik', 'Bungkus', 28000, 33000],
            ],
            'Cat' => [
                ['Cat Tembok Putih 5 kg', 'Cat tembok interior tahan jamur', 'Kaleng', 165000, 185000],
                ['Cat Kayu dan Besi 1 kg', 'Cat sintetis awet dan tidak mudah luntur', 'Kaleng', 42000, 48000],
                ['Thinner A Special 1 liter', 'Pengencer cat sintetis', 'Liter', 22000, 26000],
            ],
            'Kayu' => [
                ['Kayu Kaso 4x6 (4 m)', 'Kayu rangka atap', 'Batang', 35000, 42000],
                ['Triplek 9 mm', 'Papan lapis untuk plafon dan furnitur', 'Lembar', 95000, 110000],
                ['Kayu Jati 5x10 (4 m)', 'Kayu jati untuk kusen pintu dan jendela', 'Batang', 110000, 130000],
            ],
            'Besi' => [
                ['Besi Beton 10 mm (12 m)', 'Besi tulangan untuk kolom dan balok', 'Batang', 68000, 76000],
                ['Besi Beton 8 mm (12 m)', 'Besi tulangan untuk sloof dan praktis', 'Batang', 42000, 48000],
                ['Kawat Bendrat', 'Kawat pengikat besi beton', 'Kg', 17000, 21000],
            ],
            'Paku dan Baut' => [
                ['Paku Kayu 5 cm', 'Paku untuk rangka kayu dan bekisting', 'Kg', 17000, 21000],
                ['Paku Beton 2 inci', 'Paku untuk dinding beton dan bata', 'Kotak', 12000, 15000],
                ['Baut Baja Ringan 10 mm', 'Baut untuk rangka atap baja ringan', 'Kotak', 45000, 55000],
            ],
            'Pipa' => [
                ['Pipa PVC 3 inci (4 m)', 'Pipa air buangan', 'Batang', 52000, 60000],
                ['Pipa PVC 1/2 inci (4 m)', 'Pipa air bersih', 'Batang', 18000, 23000],
                ['Lem Pipa PVC', 'Perekat sambungan pipa PVC', 'Buah', 9000, 12000],
            ],
            'Listrik' => [
                ['Kabel NYM 2x1,5 mm (50 m)', 'Kabel instalasi listrik rumah', 'Roll', 420000, 470000],
                ['Lampu LED 12 watt', 'Lampu hemat energi cahaya putih', 'Buah', 22000, 28000],
                ['Saklar Tunggal', 'Saklar dinding standar', 'Buah', 9000, 13000],
            ],
        ];

        foreach ($catalog as $categoryName => $items) {
            $category = Category::create(['name' => $categoryName]);

            foreach ($items as [$name, $description, $unit, $cost, $price]) {
                $product = Product::factory()->create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => $description,
                    'unit' => $unit,
                    'cost_price' => $cost,
                    'price' => $price,
                ]);

                $product->suppliers()->attach(
                    $suppliers->random(rand(1, 2))->pluck('id')->mapWithKeys(
                        fn ($id) => [$id => ['supplier_price' => $cost]]
                    )->all()
                );

                if ($product->stock > 0) {
                    StockMovement::create([
                        'product_id' => $product->id, 'user_id' => $owner->id,
                        'type' => 'IN', 'quantity' => $product->stock, 'note' => 'Stok awal (seeder)',
                    ]);
                }
            }
        }
    }
}