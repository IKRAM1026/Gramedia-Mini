@extends('layout.app')

@section('content')
<form method="POST" action="{{route('login.store')}}" class="form-fieldset w-50 d-block mx-auto">
    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif
     @if(session('error'))
      <div class="alert alert-danger">
        {{ session('error') }}
      </div>
      @endif
    @csrf
  <h3 class="form-label text-center">Login</h3>
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
