@extends('layouts.argon')

@section('content')
    <div class="card shadow-lg mx-4 card-profile-bottom">
        <div class="card-body">
            <div class="container">
                <h2>{!! __('ui.create') !!} {{ __('lo.catalogo') }}</h2>
                <form action="{{ route('catalogos.store') }}" method="POST">
                    @include('catalogo._form')
                </form>
            </div>
        </div>
    </div>

@endsection