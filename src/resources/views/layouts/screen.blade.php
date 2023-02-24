@extends('laravel-profiler-deep::layouts.app')

@section("sidebar")

@endsection

@section('app-col')

    <div class="container-fluid px-3">


        <div class="row justify-content-left  ">


            @include('laravel-profiler-deep::layouts.sidebar')

            <div class="col-md-8 mt-4 mainContent">
                @if ($errors->all())
                    @foreach ($errors->all() as $V)
                        <div class="card m-4 p-4 alert alert-danger">
                            {{$V}}
                        </div>
                    @endforeach
                @endif
                @yield('content')
            </div>

        </div>
    </div>

@endsection
