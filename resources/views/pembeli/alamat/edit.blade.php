@extends('layouts.app')
@section('title', 'Edit Alamat')

@section('content')
<div class="container">
    <h4>Edit Alamat</h4>
    @include('pembeli.alamat._form', ['alamat' => $alamat])
</div>
@endsection
