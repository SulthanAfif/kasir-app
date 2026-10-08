<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'store_name' => 'Toko Maju Jaya',
            'store_address' => 'Jl. Raya No. 123, Jakarta',
            'store_phone' => '021-12345678',
            'store_email' => 'info@tokomajujaya.com',
            'tax_rate' => '11',
            'receipt_footer' => 'Terima kasih atas kunjungan Anda!',
            'currency' => 'Rp',
        ];
        foreach ($defaults as $k => $v) {
            Setting::set($k, $v);
        }

        $main = Branch::create([
            'code' => 'BR-001',
            'name' => 'Cabang Pusat',
            'address' => 'Jl. Raya No. 123, Jakarta',
            'phone' => '021-12345678',
            'is_active' => true,
            'is_main' => true,
        ]);

        Branch::create([
            'code' => 'BR-002',
            'name' => 'Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 10',
            'phone' => '022-87654321',
            'is_active' => true,
            'is_main' => false,
        ]);

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kasir.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
            'branch_id' => $main->id,
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@kasir.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
            'phone' => '081234567891',
            'branch_id' => $main->id,
        ]);

        foreach ([
            ['name' => 'Makanan', 'description' => 'Produk makanan'],
            ['name' => 'Minuman', 'description' => 'Produk minuman'],
            ['name' => 'Snack', 'description' => 'Cemilan'],
            ['name' => 'Sembako', 'description' => 'Kebutuhan pokok'],
            ['name' => 'Lainnya', 'description' => 'Lainnya'],
        ] as $cat) {
            Category::create($cat);
        }

        foreach ([
            ['code' => 'MKN001', 'name' => 'Nasi Goreng', 'category_id' => 1, 'price' => 15000, 'cost' => 8000, 'stock' => 50, 'unit' => 'porsi', 'branch_id' => $main->id],
            ['code' => 'MKN002', 'name' => 'Mie Ayam', 'category_id' => 1, 'price' => 12000, 'cost' => 6000, 'stock' => 40, 'unit' => 'porsi', 'branch_id' => $main->id],
            ['code' => 'MKN003', 'name' => 'Ayam Geprek', 'category_id' => 1, 'price' => 18000, 'cost' => 10000, 'stock' => 30, 'unit' => 'porsi', 'branch_id' => $main->id],
            ['code' => 'MNM001', 'name' => 'Es Teh Manis', 'category_id' => 2, 'price' => 5000, 'cost' => 1500, 'stock' => 100, 'unit' => 'gelas', 'branch_id' => $main->id],
            ['code' => 'MNM002', 'name' => 'Es Jeruk', 'category_id' => 2, 'price' => 7000, 'cost' => 2500, 'stock' => 80, 'unit' => 'gelas', 'branch_id' => $main->id],
            ['code' => 'MNM003', 'name' => 'Kopi Hitam', 'category_id' => 2, 'price' => 8000, 'cost' => 3000, 'stock' => 60, 'unit' => 'gelas', 'branch_id' => $main->id],
            ['code' => 'SNK001', 'name' => 'Keripik Kentang', 'category_id' => 3, 'price' => 10000, 'cost' => 6000, 'stock' => 45, 'unit' => 'pcs', 'branch_id' => $main->id],
            ['code' => 'SNK002', 'name' => 'Chitato', 'category_id' => 3, 'price' => 12000, 'cost' => 8000, 'stock' => 35, 'unit' => 'pcs', 'branch_id' => $main->id],
            ['code' => 'SMB001', 'name' => 'Beras 1kg', 'category_id' => 4, 'price' => 15000, 'cost' => 12000, 'stock' => 20, 'unit' => 'kg', 'branch_id' => $main->id],
            ['code' => 'SMB002', 'name' => 'Minyak Goreng 1L', 'category_id' => 4, 'price' => 18000, 'cost' => 15000, 'stock' => 25, 'unit' => 'botol', 'branch_id' => $main->id],
            ['code' => 'LNN001', 'name' => 'Tissue', 'category_id' => 5, 'price' => 8000, 'cost' => 5000, 'stock' => 50, 'unit' => 'pcs', 'branch_id' => $main->id],
            ['code' => 'LNN002', 'name' => 'Sabun Cuci', 'category_id' => 5, 'price' => 5000, 'cost' => 3000, 'stock' => 3, 'unit' => 'pcs', 'branch_id' => $main->id, 'min_stock' => 5],
        ] as $p) {
            Product::create($p);
        }

        Customer::create(['code' => 'CST-00001', 'name' => 'Budi Santoso', 'phone' => '081111111111']);
        Customer::create(['code' => 'CST-00002', 'name' => 'Siti Aminah', 'phone' => '082222222222']);
        Customer::create(['code' => 'CST-00003', 'name' => 'Andi Wijaya', 'phone' => '083333333333']);

        Supplier::create(['code' => 'SUP-0001', 'name' => 'CV Sumber Rezeki', 'contact_person' => 'Pak Joko', 'phone' => '081999888777', 'address' => 'Jakarta']);
        Supplier::create(['code' => 'SUP-0002', 'name' => 'UD Berkah Abadi', 'contact_person' => 'Bu Rina', 'phone' => '081888777666', 'address' => 'Tangerang']);
    }
}
