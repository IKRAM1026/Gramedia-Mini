@extends('layout.app')
@section('content')
	<div class="card mt-5 w-50 d-block mx-auto">
		<div class="card-header">
			<h1>Edit Paket Langganan</h1>
		</div>
		<div class="card-body">
			<form action="{{ route('admin.subscription-packages.update', $subscriptionPackage) }}" method="POST">
				@csrf
				@method('PUT')
				<div class="mb-3">
					<label for="nama_package" class="form-label">Nama paket langganan</label>
					<input type="text" name="nama_package" id="nama_package" class="form-control @error('nama_package') is-invalid @enderror" value="{{ old('nama_package', $subscriptionPackage->nama_package) }}" required>
					@error('nama_package')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				<div class="mb-3">
					<label for="description" class="form-label">Deskripsi</label>
					<textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description', $subscriptionPackage->description) }}</textarea>
					@error('description')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				<div class="mb-3">
					<label for="price" class="form-label">Harga</label>
					<input type="number" name="price" id="price" min="0" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $subscriptionPackage->price) }}" required>
					@error('price')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				<div class="mb-3">
					<label for="color" class="form-label">Warna</label>
					<input type="color" name="color" id="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color', $subscriptionPackage->color) }}" required>
					@error('color')
						<div class="invalid-feedback">{{ $message }}</div>
					@enderror
				</div>
				<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
			</form>
		</div>
	</div>
@endsection
