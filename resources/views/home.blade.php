@extends('layouts.app')

@section('content')
<div class="p-2">
    <transition name="slide" mode="out-in">
        <router-view>Loading...</router-view>
    </transition>
</div>
@endsection
