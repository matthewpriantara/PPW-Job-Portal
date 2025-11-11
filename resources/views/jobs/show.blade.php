@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}"><i class="fas fa-briefcase"></i> Jobs</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $job->title }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-eye"></i> {{ $job->title }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-align-left"></i> Description:</strong></p>
                            <p>{{ $job->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-building"></i> Department:</strong> {{ $job->department ?? 'N/A' }}</p>
                            <p><strong><i class="fas fa-calendar-alt"></i> Created At:</strong> {{ $job->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('jobs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Jobs</a>
                    <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning ms-2"><i class="fas fa-edit"></i> Edit Job</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
