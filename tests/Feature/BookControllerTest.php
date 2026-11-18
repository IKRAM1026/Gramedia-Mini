<?php

namespace Tests\Feature;

use App\Exports\BooksExport;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_detail_displays_attached_subscription_packages(): void
    {
        $category = BookCategory::create(['name' => 'Novel']);
        $book = $this->createBook($category, 'covers/senja.jpg');
        $package = SubscriptionPackage::create([
            'nama_package' => 'Paket Bulanan',
            'description' => 'Akses buku pilihan',
            'price' => 50000,
            'color' => '#2563eb',
        ]);
        $book->subscriptionPackages()->attach($package);

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSee('Senja')
            ->assertSee('Paket Bulanan')
            ->assertSee('Rp 50.000')
            ->assertSee('Informasi Buku')
            ->assertSee('Baca Selengkapnya');
    }

    public function test_admin_can_create_book_with_an_uploaded_cover(): void
    {
        Storage::fake('public');
        $category = BookCategory::create(['name' => 'Novel']);
        $admin = $this->adminUser();

        $response = $this->actingAs($admin)->post(route('admin.books.store'), $this->bookData($category, [
            'cover' => UploadedFile::fake()->image('senja.jpg'),
        ]));

        $response->assertRedirect(route('admin.books.index'));
        $book = Book::query()->firstOrFail();
        $this->assertStringStartsWith('/storage/covers/', $book->cover);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'page_of_book' => 239,
            'description' => '<p>Deskripsi buku</p>',
        ]);
        Storage::disk('public')->assertExists(substr($book->cover, strlen('/storage/')));
    }

    public function test_edit_page_renders_saved_cover_description_and_selected_options(): void
    {
        Storage::fake('public');
        $category = BookCategory::create(['name' => 'Novel']);
        $book = $this->createBook($category, 'covers/senja.jpg');
        Storage::disk('public')->put('covers/senja.jpg', 'cover');

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.books.edit', $book));

        $response->assertOk()
            ->assertSee('storage/covers/senja.jpg')
            ->assertSee('value="'.$category->id.'" selected', false)
            ->assertSee('value="English" selected', false)
            ->assertSee('Deskripsi lama')
            ->assertSee('dangerouslyPasteHTML');
    }

    public function test_admin_can_update_a_book_without_replacing_its_cover(): void
    {
        Storage::fake('public');
        $category = BookCategory::create(['name' => 'Novel']);
        Storage::disk('public')->put('covers/senja.jpg', 'cover');
        $book = $this->createBook($category, 'covers/senja.jpg');

        $response = $this->actingAs($this->adminUser())
            ->put(route('admin.books.update', $book), $this->bookData($category, [
                'cover' => null,
                'title' => 'Senja Terbaru',
                'description' => '<p>Deskripsi diperbarui</p>',
            ]));

        $response->assertRedirect(route('admin.books.index'));
        $book->refresh();
        $this->assertSame('/storage/covers/senja.jpg', $book->cover);
        $this->assertSame('Senja Terbaru', $book->title);
        $this->assertSame('<p>Deskripsi diperbarui</p>', $book->description);
        Storage::disk('public')->assertExists('covers/senja.jpg');
    }

    public function test_admin_can_replace_a_book_cover_and_the_previous_file_is_removed(): void
    {
        Storage::fake('public');
        $category = BookCategory::create(['name' => 'Novel']);
        Storage::disk('public')->put('covers/senja-lama.jpg', 'old cover');
        $book = $this->createBook($category, 'covers/senja-lama.jpg');

        $response = $this->actingAs($this->adminUser())
            ->put(route('admin.books.update', $book), $this->bookData($category, [
                'cover' => UploadedFile::fake()->image('senja-baru.jpg'),
            ]));

        $response->assertRedirect(route('admin.books.index'));
        $book->refresh();
        $this->assertNotSame('/storage/covers/senja-lama.jpg', $book->cover);
        Storage::disk('public')->assertExists(substr($book->cover, strlen('/storage/')));
        Storage::disk('public')->assertMissing('covers/senja-lama.jpg');
    }

    public function test_admin_can_download_books_with_the_required_excel_data(): void
    {
        $category = BookCategory::create(['name' => 'Sci-Fi']);
        $book = $this->createBook($category, 'covers/bintang.jpg');
        $book->update([
            'title' => 'Bintang',
            'writer' => 'Tere Liye',
            'publisher' => 'Gramedia',
            'price' => 99000,
            'release_date' => '2024-09-27',
            'page_of_book' => 380,
            'language' => 'Indonesia',
        ]);
        Excel::fake();

        $response = $this->actingAs($this->adminUser())
            ->get(route('admin.books.export'));

        $response->assertOk();
        Excel::assertDownloaded('books.xlsx', function (BooksExport $export) use ($category): bool {
            $book = $export->collection()->first();
            $styles = $export->styles(new Worksheet);

            return $export->headings() === [
                'Nomor',
                'Alamat Gambar Sampul Buku',
                'Nama Kategori Buku',
                'Judul Buku',
                'Penulis',
                'Penerbit',
                'Tanggal Rilis',
                'Jumlah Halaman',
                'Bahasa',
                'Harga',
            ] && $export->map($book) === [
                1,
                asset('/storage/covers/bintang.jpg'),
                $category->name,
                'Bintang',
                'Tere Liye',
                'Gramedia',
                '27 September 2024',
                '380',
                'Indonesia',
                'Rp 99.000',
            ] && ($styles[1]['font']['bold'] ?? false)
                && $styles['A1:J2']['borders']['allBorders']['borderStyle'] === Border::BORDER_THIN;
        });
    }

    public function test_non_admin_cannot_download_excel_exports(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.books.export'))
            ->assertRedirect(route('home'));

        $this->get(route('admin.subscription-packages.export'))
            ->assertRedirect(route('home'));
    }

    private function adminUser(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function bookData(BookCategory $category, array $overrides = []): array
    {
        return array_merge([
            'title' => 'Senja',
            'writer' => 'Boy Candra',
            'publisher' => 'Mediakita',
            'price' => 75000,
            'release_date' => '2026-10-22',
            'page_of_book' => 239,
            'language' => 'English',
            'book_category_id' => $category->id,
            'description' => '<p>Deskripsi buku</p>',
        ], $overrides);
    }

    private function createBook(BookCategory $category, string $cover): Book
    {
        return Book::create($this->bookData($category, [
            'cover' => '/storage/'.$cover,
            'description' => '<p>Deskripsi lama</p>',
        ]));
    }
}
