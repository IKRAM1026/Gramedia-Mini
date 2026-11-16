<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

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
