@extends('layouts.app')

@section('title', 'Detail Rating Penitip')

@section('content')
<div class="container mt-5">
    <div class="card text-center">
        <div class="card-body">
            <img src="{{ asset($penitip->FOTO_PROFIL ?? 'images/default-profile.png') }}" class="rounded-circle mb-3" width="100" height="100" alt="Foto Profil">
            <h3>{{ $penitip->NAMA_PENITIP }}</h3>
            <div class="text-warning my-3">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $penitip->RATING_PENITIP)
                        <i class="fas fa-star"></i>
                    @elseif($i <= $penitip->RATING_PENITIP + 0.5)
                        <i class="fas fa-star-half-alt"></i>
                    @else
                        <i class="far fa-star"></i>
                    @endif
                @endfor
            </div>
            <p class="fs-5">Rata-rata: {{ number_format($penitip->RATING_PENITIP, 1) }}/5.0</p>
        </div>
    </div>
</div>
@endsection
