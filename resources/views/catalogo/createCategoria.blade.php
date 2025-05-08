@extends('layouts.argon')

@section('content')
    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body">
            <div class="container">
                <h2><i class="fas fa-plus-circle"></i> {{ __('ui.create_text') }} {{ __('lo.categoria') }}</h2>
                <form action="{{ route('categorias.store') }}" method="POST">
                    @include('catalogo._formCategoria')
                </form>
            </div>
        </div>
    </div>

@endsection