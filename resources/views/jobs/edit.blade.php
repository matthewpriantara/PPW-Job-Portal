@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}"><i class="fas fa-briefcase"></i> Jobs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit {{ $job->title }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0"><i class="fas fa-edit"></i> Edit Job</h4>
                </div>
                <div class="card-body">
                    <form id="edit-form" method="POST" action="{{ route('jobs.update', $job) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label"><i class="fas fa-tag"></i> Job Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $job->title) }}" required>
                            @error('title')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left"></i> Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $job->description) }}</textarea>
                            @error('description')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label"><i class="fas fa-building"></i> Department</label>
                            <input type="text" name="department" id="department" class="form-control" value="{{ old('department', $job->department) }}">
                            @error('department')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Jobs</a>
                            <div>
                                <form method="POST" action="{{ route('jobs.destroy', $job) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger me-2"><i class="fas fa-trash"></i> Delete Job</button>
                                </form>
                                <button type="submit" form="edit-form" class="btn btn-success"><i class="fas fa-save"></i> Update Job</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
