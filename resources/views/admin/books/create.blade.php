@extends("layout.app")

@section("content")
   <form action="{{route('admin.books.store')}}" method="POST" enctype="multipart/form-data" class="d-block mx-auto w-75 mt-5 card">
        @csrf
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
            <h3>Tambah Buku</h3>
        </div>
        <div class="card-body">
            <!-- Baris Pertama -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="title" class="form-label">Judul Buku</label>
                    <input type="text" name="title" id="title" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="writer" class="form-label">Penulis</label>
                    <input type="text" name="writer" id="writer" class="form-control">
                </div>
            </div>
            <!-- Baris Kedua  -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="publisher" class="form-label">Penerbit</label>
                    <input type="text" name="publisher" id="publisher" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="price" class="form-label">Harga</label>
                    <input type="number" name="price" id="price" class="form-control">
                </div>
            </div>
            <!-- Baris Ketiga -->
            <div class="row mb-3">
                <div class="col-6">
                    <label for="release_date" class="form-label">Tanggal rilis</label>
                    <input type="date" name="release_date" id="release_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="page_of_book" class="form-label">Halaman Buku</label>
                    <input type="text" name="page_of_book" id="page_of_book" class="form-control">
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
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label for="language" class="form-label">Bahasa</label>
                    {{--Data bahasa tidak ada di db jadi pilihan di buat manual--}}
                    <select name="language" id="language" class="form-select">
                        <option disabled hidden selected>Pilih</option>
                        <option value="Indonesian">Indonesian</option>
                        <option value="English">English</option>
                        <option value="Arabic">Arabic</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="cover" class="form-label">Sampul buku</label>
                    <input type="file" name="cover" id="cover" class="form-control">
                </div>
            </div>
            <div class="row mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <div id="description"></div>
                {{--input yang disembuyikan untuk menyimpan isi dari quilljs--}}
                <input type="hidden" name="description" id="description-input">
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
  const quill = new Quill('#description', {
    theme: 'snow'
  });

  //untuk menyimpan isian dari quill js ke unput hidden 
  document.getElementById('description-input').value = quill.root.innerHTML;
  //ketika isi di quill berubah, update juga ke unut hiddennya
  quill.on('text-change', function() {
    document.getElementById('description-input').value = quill.root.innerHTML;
  });
</script>
@endpush
            