<h5>Applicant Dashboard</h5>
    <p>Welcome, Applicant! You can apply for jobs here.</p>

    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button" role="tab" aria-controls="jobs" aria-selected="true">Available Jobs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button" role="tab" aria-controls="applications" aria-selected="false">My Applications</button>
        </li>
    </ul>
    <div class="tab-content" id="dashboardTabsContent">
        <div class="tab-pane fade show active" id="jobs" role="tabpanel" aria-labelledby="jobs-tab">
            @if(isset($jobVacancies) && $jobVacancies->count() > 0)
                <h4 class="mb-3 mt-3">Available Job Vacancies</h4>
                <div class="row">
                    @foreach($jobVacancies as $vacancy)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">{{ $vacancy->job->title }}</h5>
                                    <small>{{ $vacancy->position }}</small>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $vacancy->job->description }}</p>
                                    <p class="text-muted">
                                        <strong>Department:</strong> {{ $vacancy->job->department }}<br>
                                        <strong>Salary:</strong> ${{ number_format($vacancy->salary) }}<br>
                                        <strong>Deadline:</strong> {{ $vacancy->deadline->format('d M Y') }}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modal-{{ $vacancy->id }}">
                                        View Details & Apply
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for Job Details and Apply -->
                        <div class="modal fade" id="modal-{{ $vacancy->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $vacancy->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalLabel-{{ $vacancy->id }}">{{ $vacancy->job->title }} - {{ $vacancy->position }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Description:</strong> {{ $vacancy->job->description }}</p>
                                        <p><strong>Department:</strong> {{ $vacancy->job->department }}</p>
                                        <p><strong>Salary:</strong> ${{ number_format($vacancy->salary) }}</p>
                                        <p><strong>Deadline:</strong> {{ $vacancy->deadline->format('d M Y') }}</p>

                                        <hr>
                                        <h6>Apply for this Job</h6>
                                        <form enctype="multipart/form-data" method="POST" action="{{ route('apply', $vacancy->id) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="cv-modal-{{ $vacancy->id }}" class="form-label">Upload CV (PDF, DOC, DOCX, max 2MB)</label>
                                                <input type="file" name="cv" id="cv-modal-{{ $vacancy->id }}" class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-success">Submit Application</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info mt-3">No job vacancies available at the moment.</div>
            @endif
        </div>
        <div class="tab-pane fade" id="applications" role="tabpanel" aria-labelledby="applications-tab">
            @if(isset($myApplications) && $myApplications->count() > 0)
                <h4 class="mt-3">My Applications</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Job Title</th>
                                <th>Position</th>
                                <th>Status</th>
                                <th>Applied At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myApplications as $application)
                                <tr>
                                    <td>{{ $application->jobVacancy->job->title }}</td>
                                    <td>{{ $application->jobVacancy->position }}</td>
                                    <td>
                                        <span class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $application->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mt-3">You haven't applied for any jobs yet.</div>
            @endif
        </div>
    </div>
