@extends('layouts.app')

@section('title', 'News')

@section('content')
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Negara</label>
                    <select class="form-select" id="select-news-country">
                        <option value="">-- Pilih negara --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <select class="form-select" id="select-news-category">
                        <option value="logistics">Logistik</option>
                        <option value="geopolitical">Geopolitik</option>
                        <option value="economic">Ekonomi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" id="btn-search-news">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari Berita
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="news-results"></div>
@endsection

@push('scripts')
<script src="{{ asset('js/news-page.js') }}"></script>
@endpush