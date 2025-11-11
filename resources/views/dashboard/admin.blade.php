@if(Auth::user()->isAdmin())
    <h5>Admin Dashboard</h5>
    <p>Welcome, Admin! You have access to administrative features.</p>

    <ul class="nav nav-tabs" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications"
                    type="button" role="tab" aria-controls="applications" aria-selected="true">Applications
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button" role="tab"
                    aria-controls="jobs" aria-selected="false">Manage Jobs
            </button>
        </li>
    </ul>
    <div class="tab-content" id="adminTabsContent">
        <div class="tab-pane fade show active" id="applications" role="tabpanel" aria-labelledby="applications-tab">
            @if(isset($applications) && $applications->count() > 0)
                <!-- Job Applications Table -->
                <h4 class="mt-4 mb-3">Job Applications</h4>

                <!-- Filter Form -->
                <form method="GET" action="{{ route('dashboard') }}" class="mb-3 d-flex align-items-end">
                    <div class="me-2">
                        <label for="job_filter" class="form-label">Filter by Job Vacancy:</label>
                        <select name="job_id" id="job_filter" class="form-select">
                            <option value="">All Job Vacancies</option>
                            @foreach(\App\Models\JobVacancy::with('job')->get() as $vacancy)
                                <option
                                    value="{{ $vacancy->id }}" {{ request('job_id') == $vacancy->id ? 'selected' : '' }}>
                                    {{ $vacancy->job->title }} - {{ $vacancy->position }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('export.applications', request()->query()) }}" class="btn btn-secondary">Export to
                        CSV</a>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="table-dark">
                        <tr>
                            <th><i class="fas fa-user"></i> Applicant</th>
                            <th><i class="fas fa-tag"></i> Job Title</th>
                            <th><i class="fas fa-briefcase"></i> Position</th>
                            <th><i class="fas fa-check-circle"></i> Status</th>
                            <th><i class="fas fa-file-pdf"></i> CV</th>
                            <th><i class="fas fa-calendar-alt"></i> Applied At</th>
                            <th><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($applications as $application)
                            <tr>
                                <td>{{ $application->user->name }}</td>
                                <td>{{ $application->jobVacancy->job->title }}</td>
                                <td>{{ $application->jobVacancy->position }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($application->status) {
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                </td>
                                <td>
                                    <a href="{{ asset('storage/' . $application->cv) }}" target="_blank"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View CV
                                    </a>
                                </td>
                                <td>{{ $application->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($application->status === 'pending')
                                        <div class="btn-group" role="group">
                                            <form action="{{ route('approve', $application->id) }}" method="POST"
                                                  class="d-inline">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Are you sure you want to approve this application?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('reject', $application->id) }}" method="POST"
                                                  class="d-inline">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to reject this application?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> No applications submitted yet.
                </div>
            @endif
        </div>
        <div class="tab-pane fade" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">

            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="mt-4 mb-3"> Manage Jobs</h2>
                            <div>
                                <a href="{{ route('jobs.template') }}" class="btn btn-light btn-sm me-2">
                                    <i class="fas fa-download"></i> Download Template
                                </a>
                                <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal"
                                        data-bs-target="#importModal">
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
                                        <th> Title</th>
                                        <th> Description</th>
                                        <th> Department</th>
                                        <th> Created At</th>
                                        <th> Actions</th>
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
                                                <a href="{{ route('jobs.show', $job) }}"
                                                   class="btn btn-info btn-sm me-1">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('jobs.edit', $job) }}"
                                                   class="btn btn-warning btn-sm me-1">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form method="POST" action="{{ route('jobs.destroy', $job) }}"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this job?')">
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


            <!-- Import Jobs Modal -->
            <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="importModalLabel"><i class="fas fa-upload"></i> Import Jobs from
                                CSV</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Upload a CSV file with columns: <strong>title</strong>, <strong>description</strong>,
                                <strong>department</strong>.</p>
                            <form action="{{ route('jobs.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="file" class="form-label"><i class="fas fa-file-csv"></i> Select CSV File</label>
                                    <input type="file" class="form-control" id="file" name="file"
                                           accept=".csv,.xlsx,.xls" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                            class="fas fa-times"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endif
