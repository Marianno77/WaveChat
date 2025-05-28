@extends('layouts.app')

@section('content')
    <div class='messages'>
        <div id="app">
            <messages :conversation='@json($conversation)' :friend-name='@json($friendName)' :user-id='@json($userId)'>
            </messages>
        </div>
    </div>
@endsection