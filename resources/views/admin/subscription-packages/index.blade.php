@extends('layout.app')

@section('content')
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Paket Langganan</h1>
            <a href="{{ route('admin.subscription-packages.create') }}" class="btn btn-primary mb-3">Tambah Paket Langganan</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <table id="subscription-packages-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Paket</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Warna</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#subscription-packages-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.subscription-packages.index') }}",
           columns: [
    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'nama_package', name: 'nama_package' },
    { data: 'description', name: 'description' },
    { data: 'price', name: 'price' },
    {
        data: 'color',
        name: 'color',
        render: function (data) {
            return '<div style="background-color: ' + data + '; width: 40px; height: 25px; border: 1px solid #ccc; border-radius: 4px;"></div>';
        }
    },
    { data: 'action', name: 'action', orderable: false, searchable: false }
]
        });
    });
</script>
@endpush