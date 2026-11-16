@extends("layout.app")

@section("content")
   <form action="{{route('admin.books.update', $book->id)}}" method="POST" enctype="multipart/form-data" class="d-block mx-auto w-75 mt-5 card">
        @csrf
        @method('PUT')
        {{--pengganti error untuk memunculkan semua error sekaligus--}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-header">
            <h3>Edit Buku</h3>
        </div>
        <div class="card-body">
            <!-- Baris Pertama -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Judul Buku</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $book->title) }}">
                </div>
                <div class="col-md-6">
                    <label for="writer" class="form-label">Penulis</label>
                    <input type="text" name="writer" id="writer" class="form-control" value="{{ old('writer', $book->writer) }}">
                </div>
            </div>
            <!-- Baris Kedua  -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="publisher" class="form-label">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" class="form-control" value="{{ old('publisher', $book->publisher) }}">
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" name="price" id="price" class="form-control" value="{{ old('price', $book->price) }}">
                </div>
            </div>
            <!-- Baris Ketiga -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="release_date" class="form-label">Tanggal rilis</label>
                    <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date', $book->release_date) }}">
                </div>
                <div class="col-md-6">
                    <label for="page_of_book" class="form-label">Halaman Buku</label>
                    <input type="text" name="page_of_book" id="page_of_book" class="form-control" value="{{ old('page_of_book', $book->page_of_book) }}">
                </div>
            </div>

             <div class="row mb-3">
                <div class="col-6">
                    <label for="book_category_id" class="form-label">Kategori Buku</label>
                    <select name="book_category_id" id="book_category_id" class="form-select">
                            <option value="">Pilih Kategori</option>
                            {{--looping untuk munculkan kaegori yang udah di abil dari db oleh controller--}}
                            @foreach ($bookCategory as $item)
                            {{--karena book_category_id mintanya _id jd yang didkirim ke value merupakan idnya--}}
                                <option value="{{ $item->id }}" @selected(old('book_category_id', $book->book_category_id) == $item->id)>{{ $item->name }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label for="language" class="form-label">Bahasa</label>
                    {{--Data bahasa tidak ada di db jadi pilihan di buat manual--}}
                    <select name="language" id="language" class="form-select">
                        <option disabled hidden value="">Pilih</option>
                        <option value="Indonesian" @selected(old('language', $book->language) === 'Indonesian')>Indonesian</option>
                        <option value="English" @selected(old('language', $book->language) === 'English')>English</option>
                        <option value="Arabic" @selected(old('language', $book->language) === 'Arabic')>Arabic</option>
                    </select>
                </div>
            </div>
                {{--sampul buku--}}
            <div class="row mb-3">
                <div class="col-12">    
                    <label for="cover" class="form-label">Sampul buku</label>
                    @if ($book->cover)
                        <div class="mb-2">
                            <img src="{{ asset($book->cover) }}" alt="Sampul {{ $book->title }}" class="img-thumbnail" style="max-height: 120px">
                            <div class="text-muted d-block mt-1"> Bila ingin mengganti sampul silahkan pilih file baru</div>
                        </div>
                    @endif
                    <input type="file" name="cover" id="cover" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <div id="description"></div>
                {{--input yang disembuyikan untuk menyimpan isi dari quilljs--}}
                <input type="hidden" name="description" id="description-input" value="{{ old('description', $book->description) }}">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.8/purify.min.js"></script>
<script>
  const quill = new Quill('#description', {
    theme: 'snow'
  });
  const descriptionInput = document.getElementById('description-input');

  quill.clipboard.dangerouslyPasteHTML(DOMPurify.sanitize(descriptionInput.value || ''));
  quill.on('text-change', function() {
    descriptionInput.value = quill.root.innerHTML;
  });
</script>
@endpush
            