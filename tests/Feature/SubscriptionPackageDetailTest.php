<?php

namespace Tests\Feature;

use App\Exports\SubscriptionPackagesExport;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\SubscriptionPackage;
use App\Models\SubscriptionPackageUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class SubscriptionPackageDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_checkout_and_see_subscription_history(): void
    {
        $this->travelTo('2026-11-05 09:10:13');
        $user = User::factory()->create(['name' => 'Fema']);
        $package = SubscriptionPackage::create([
            'nama_package' => 'Paket Bulanan',
            'description' => 'Paket langganan bulanan',
            'price' => 50000,
            'color' => '#2563eb',
        ]);

        $checkoutPage = $this->actingAs($user)
            ->get(route('subscription-packages.checkout.page', $package));

        $checkoutPage->assertOk()
            ->assertSee('Scan QR Code')
            ->assertSee('Pembayaran');

        $response = $this->actingAs($user)
            ->post(route('subscription-packages.checkout', $package));

        $response->assertRedirect(route('account'));
        $this->assertDatabaseHas('subscription_package_users', [
            'subscription_package_id' => $package->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('account'))
            ->assertOk()
            ->assertSee('Fema')
            ->assertSee('Paket Bulanan')
            ->assertSee('Jenis Transaksi')
            ->assertSee('Paket Langganan')
            ->assertSee('Koleksi Buku')
            ->assertDontSee('Riwayat Buku');
    }

    public function test_guest_cannot_checkout_a_package(): void
    {
        $package = SubscriptionPackage::create([
            'nama_package' => 'Paket Tamu',
            'description' => 'Paket langganan tamu',
            'price' => 50000,
            'color' => '#2563eb',
        ]);

        $response = $this->get(route('subscription-packages.checkout.page', $package));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('subscription_package_users', [
            'subscription_package_id' => $package->id,
        ]);
    }

    public function test_package_detail_displays_books_attached_through_the_many_to_many_relation(): void
    {
        $package = SubscriptionPackage::create([
            'nama_package' => 'Fiction Package',
            'description' => 'Buku-buku fiksi pilihan',
            'price' => 79000,
            'color' => '#ef4444',
        ]);
        $firstBook = $this->createBook('The Great Gatsby', 'F. Scott Fitzgerald');
        $secondBook = $this->createBook('Little Women', 'Louisa May Alcott');
        $package->books()->attach([$firstBook->id, $secondBook->id]);

        $response = $this->get(route('subscription-packages.show', $package));

        $response->assertOk()
            ->assertSee('Fiction Package')
            ->assertSee('The Great Gatsby')
            ->assertSee('Little Women');
        $this->assertSame(2, $package->books()->count());
        $this->assertTrue($firstBook->subscriptionPackages()->whereKey($package->id)->exists());
    }

    public function test_package_detail_can_search_books_by_title_or_writer(): void
    {
        $package = SubscriptionPackage::create([
            'nama_package' => 'Novel Package',
            'description' => 'Pilihan novel',
            'price' => 69000,
            'color' => '#2563eb',
        ]);
        $matchingBook = $this->createBook('Senja di Jakarta', 'Ahmad Tohari');
        $otherBook = $this->createBook('Matahari Pagi', 'Andrea Hirata');
        $package->books()->attach([$matchingBook->id, $otherBook->id]);

        $response = $this->get(route('subscription-packages.show', [
            'subscriptionPackage' => $package,
            'search' => 'Ahmad',
        ]));

        $response->assertOk()
            ->assertSee('Senja di Jakarta')
            ->assertDontSee('Matahari Pagi');
    }

    public function test_admin_can_attach_books_from_the_package_book_page(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $firstBook = $this->createBook('Buku Pertama', 'Penulis Pertama');
        $secondBook = $this->createBook('Buku Kedua', 'Penulis Kedua');

        $package = SubscriptionPackage::create([
            'nama_package' => 'Reader Package',
            'description' => 'Paket untuk pembaca',
            'price' => 99000,
            'color' => '#22c55e',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.subscription-packages.books.attach', $package), [
            'book_ids' => [$firstBook->id, $secondBook->id],
        ]);

        $response->assertRedirect(route('admin.subscription-packages.books.index', $package));
        $this->assertSame(
            [$firstBook->id, $secondBook->id],
            $package->books()->orderBy('books.id')->pluck('books.id')->all(),
        );
    }

    public function test_admin_can_download_subscription_package_book_and_active_customer_counts(): void
    {
        $this->travelTo('2026-11-17 09:00:00');
        $package = SubscriptionPackage::create([
            'nama_package' => 'Premium',
            'description' => 'Paket premium',
            'price' => 150000,
            'color' => '#2563eb',
        ]);
        $firstBook = $this->createBook('Buku Satu', 'Penulis Satu');
        $secondBook = $this->createBook('Buku Dua', 'Penulis Dua');
        $package->books()->attach([$firstBook->id, $secondBook->id]);
        $activeUser = User::factory()->create();
        $expiredUser = User::factory()->create();
        SubscriptionPackageUser::create([
            'subscription_package_id' => $package->id,
            'user_id' => $activeUser->id,
            'expired_date' => '2026-11-17',
        ]);
        SubscriptionPackageUser::create([
            'subscription_package_id' => $package->id,
            'user_id' => $expiredUser->id,
            'expired_date' => '2026-11-16',
        ]);
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        Excel::fake();

        $response = $this->actingAs($admin)
            ->get(route('admin.subscription-packages.export'));

        $response->assertOk();
        Excel::assertDownloaded('subscription_packages.xlsx', function (SubscriptionPackagesExport $export): bool {
            $package = $export->collection()->first();
            $styles = $export->styles(new Worksheet);

            return $export->headings() === [
                'Nomor',
                'Nama Paket Langganan',
                'Harga',
                'Jumlah Buku',
                'Pelanggan Aktif',
            ]
                    && $export->map($package) === [1, 'Premium', 'Rp 150.000', 2, 1]
                    && ($styles[1]['font']['bold'] ?? false)
                    && $styles['A1:E2']['borders']['allBorders']['borderStyle'] === Border::BORDER_THIN;
        });
    }

    public function test_attached_books_are_not_shown_in_the_add_book_selection(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $package = SubscriptionPackage::create([
            'nama_package' => 'Selected Package',
            'description' => 'Paket pilihan',
            'price' => 89000,
            'color' => '#0ea5e9',
        ]);
        $attachedBook = $this->createBook('Buku Sudah Dipilih', 'Penulis Lama');
        $availableBook = $this->createBook('Buku Masih Bisa Dipilih', 'Penulis Baru');
        $package->books()->attach($attachedBook);

        $response = $this->actingAs($admin)->get(route('admin.subscription-packages.books.add', $package));

        $response->assertOk()
            ->assertSee('Buku Masih Bisa Dipilih')
            ->assertDontSee('Buku Sudah Dipilih');
    }

    public function test_user_can_checkout_a_book_and_see_it_in_profile_history(): void
    {
        $user = User::factory()->create(['name' => 'Pembeli Buku']);
        $book = $this->createBook('QR Checkout Book', 'Penulis QR');

        $checkoutPage = $this->actingAs($user)
            ->get(route('books.checkout.page', $book));

        $checkoutPage->assertOk()
            ->assertSee('Scan QR Code')
            ->assertSee('QR Checkout Book');

        $response = $this->actingAs($user)
            ->post(route('books.checkout', $book));

        $response->assertRedirect(route('account'));
        $this->assertDatabaseHas('checkouts', [
            'user_id' => $user->id,
            'total_book' => 1,
            'total_price' => 75000,
        ]);
        $this->assertDatabaseHas('checkout_books', [
            'book_id' => $book->id,
            'total_book' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('account', ['type' => 'book']))
            ->assertOk()
            ->assertSee('QR Checkout Book')
            ->assertSee('Koleksi Buku');
    }

    private function createBook(string $title, string $writer): Book
    {
        $category = BookCategory::query()->firstOrCreate(['name' => 'Novel']);

        return Book::create([
            'cover' => '/storage/covers/example.jpg',
            'title' => $title,
            'price' => 75000,
            'description' => 'Deskripsi buku',
            'language' => 'Indonesia',
            'publisher' => 'Gramedia',
            'writer' => $writer,
            'release_date' => '2026-10-22',
            'page_of_book' => 200,
            'book_category_id' => $category->id,
        ]);
    }
}
