<?php

    namespace App\Http\Controllers\ProfilerDeep;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Validator;

    class ProfilerDeepController extends Controller
    {


        public function index() {


            return view('laravel-profiler-deep.example');

        }
    }
