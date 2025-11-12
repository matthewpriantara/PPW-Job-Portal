@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-plus"></i> Create New Job</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('jobs.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label"><i class="fas fa-tag"></i> Job Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label"><i class="fas fa-align-left"></i> Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="department" class="form-label"><i class="fas fa-building"></i> Department</label>
                            <input type="text" name="department" id="department" class="form-control" value="{{ old('department') }}">
                            @error('department')
                                <div class="text-danger"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('jobs.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Jobs</a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Create Job</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
