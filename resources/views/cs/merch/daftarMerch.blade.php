@extends('layouts.cs')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Daftar Merchandise</h2>

    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Jenis Merchandise</th>
                    <th scope="col">Poin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($merchandiseList as $merch)
                <tr>
                    <td class="text-center">{{ $merch->ID_MERCHANDISE }}</td>
                    <td>{{ $merch->JENIS_MERCHANDISE }}</td>
                    <td class="text-center">{{ number_format($merch->POINT, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
