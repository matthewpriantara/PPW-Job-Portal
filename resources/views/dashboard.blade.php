@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>
                    @error('cv')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(Auth::user()->isAdmin())
                            @include('dashboard.admin')
                        @elseif(Auth::user()->isApplicant())
                            @include('dashboard.applicant')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
