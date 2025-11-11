@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0"><i class="fas fa-briefcase"></i> Manage Jobs</h2>
                        <div>
                            <a href="{{ route('jobs.template') }}" class="btn btn-light btn-sm me-2">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                            <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-upload"></i> Import Jobs
                            </button>
                            <a href="{{ route('jobs.create') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-plus"></i> Create New Job
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($jobs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="fas fa-tag"></i> Title</th>
                                        <th><i class="fas fa-align-left"></i> Description</th>
                                        <th><i class="fas fa-building"></i> Department</th>
                                        <th><i class="fas fa-calendar-alt"></i> Created At</th>
                                        <th><i class="fas fa-cogs"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jobs as $job)
                                        <tr>
                                            <td>{{ $job->title }}</td>
                                            <td>{{ Str::limit($job->description, 50) }}</td>
                                            <td>{{ $job->department ?? 'N/A' }}</td>
                                            <td>{{ $job->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('jobs.show', $job) }}" class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning btn-sm me-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form method="POST" action="{{ route('jobs.destroy', $job) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job?')">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <h5>No jobs created yet.</h5>
                            <p>Get started by creating your first job!</p>
                            <a href="{{ route('jobs.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Job
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Jobs Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="importModalLabel"><i class="fas fa-upload"></i> Import Jobs from CSV</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Upload a CSV file with columns: <strong>title</strong>, <strong>description</strong>, <strong>department</strong>.</p>
                <form action="{{ route('jobs.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label"><i class="fas fa-file-csv"></i> Select CSV File</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
