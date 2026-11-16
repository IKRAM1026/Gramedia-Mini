@extends('layout.app')

@section('content')
<form method="POST" action="{{route('register.store') }}" class="form-fieldset w-50 d-block mx-auto">
    @csrf
  <h3 class="form-label text-center">REGISTER</h3>
  <div class="mb-3">
    <label class="form-label required" for="fieldset-name">Nama Lengkap</label>
    <input type="text" id="fieldset-name" class="form-control" @error('name') is-invalid @enderror autocomplete="name" required  name="name" />
    @error("name")
    <small class="text-danger">{{ $message }}</small>      
    @enderror
  </div>
  <div class="mb-3">
    <label class="form-label required" for="fieldset-email">Email</label>
    <input type="email" id="fieldset-email" class="form-control" @error('email') is-invalid @enderror autocomplete="email" required name="email"/>
        @error("email")
    <small class="text-danger">{{ $message }}</small>       
    @enderror
  </div>
  <div class="mb-3">
    <label class="form-label" for="fieldset-phone">Password</label>
    <input type="Password" id="fieldset-phone" class="form-control" @error('password') is-invalid @enderror autocomplete="tel" name="password" />
        @error("password")
    <small class="text-danger">{{ $message }}</small>       
    @enderror
  </div>
  <button type="submit" class="btn btn-primary">Kirim</button>
</form>
@endsection
