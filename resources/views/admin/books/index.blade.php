@extends('layout.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between">
            <h1>Buku</h1>
            <a href="{{ route('admin.books.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <div class="table table-bordered mt-5">
            <table class="table table-bordered" id="data-buku">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Sampul</th>
                        <th>Judul</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    {{-- Modal detail --}}
    <div class="modal" id="modalDetailBook" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Informasi Buku</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4 align-items-start">
                        <div class="col-12 col-md-4 text-center">
                            <img id="detail-cover" src="" alt="Cover Buku"
                                class="rounded shadow-sm border img-fluid"
                                style="max-height: 320px; object-fit: contain;">
                        </div>
                        <div class="col-12 col-md-8">
                            <h3 id="detail-title" class="mb-2 text-primary fw-bold"></h3>
                            <div class="mb-3">
                                <span id="detail-category" class="badge bg-blue-lt"></span>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="small text-secondary">Harga</div>
                                    <div class="text-success fw-bold" id="detail-price"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-secondary">Penulis</div>
                                    <div id="detail-writer" class="text-break"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-secondary">Penerbit</div>
                                    <div id="detail-publisher" class="text-break"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-secondary">Bahasa</div>
                                    <div id="detail-language" class="text-break"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-secondary">Tanggal Rilis</div>
                                    <div id="detail-release"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small text-secondary">Jumlah Halaman</div>
                                    <div id="detail-page_of_book"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h4 class="subheader mb-2">Deskripsi Buku</h4>
                        <div id="detail-description" class="p-3 bg-body-tertiary rounded border"
                            style="max-height: 200px; overflow-y: auto; white-space: pre-line;">
                            <span class="text-secondary">Tidak ada deskripsi yang tersedia.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-***" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('#data-buku').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.books.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'coverImg',
                        name: 'coverImg',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'category_name',
                        name: 'category_name'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            
            $('#data-buku').on('click', '#btn-detail', function() {
                const btn = $(this);

                //ambil data dari data attribute
                const cover = btn.data('cover');
                const title = btn.data('title');
                const category = btn.data('category');
                const price = btn.data('price');
                const writer = btn.data('writer');
                const publisher = btn.data('publisher');
                const language = btn.data('language');
                const page_of_book = btn.data('pages');
                const release = btn.data('release');
                const description = btn.data('description');

                $('#detail-cover').attr('src', cover);
                $('#detail-title').text(title);
                $('#detail-category').text(category);
                $('#detail-price').text('Rp ' + Number(price).toLocaleString('id-ID'));
                $('#detail-writer').text(writer);
                $('#detail-publisher').text(publisher);
                $('#detail-language').text(language);
                $('#detail-page_of_book').text(page_of_book);
                $('#detail-release').text(release);
                const parsedDescription = new DOMParser().parseFromString(description || '', 'text/html');
                const descriptionText = parsedDescription.body.innerText.trim();
                $('#detail-description').text(descriptionText || 'Tidak ada deskripsi yang tersedia.');

                const modalElement = document.getElementById('modalDetailBook');
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            });
        });
    </script>
@endpush
