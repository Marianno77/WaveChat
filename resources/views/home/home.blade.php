@extends('layouts.app')

@section('content')
    <div class="home">
        <div id="app">
            <home register-url="{{ route('register') }}"></home>
        </div>
    </div>
@endsection