@extends('layouts.app')

@section('title', 'YouTube Downloads')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-list-ul"></i> Downloads</h1>
            <a href="{{ route('downloads.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Download
            </a>
        </div>

        @if($downloads->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-download display-1 text-muted"></i>
                    <h4 class="mt-3">No downloads yet</h4>
                    <p class="text-muted">Start by downloading your first YouTube video!</p>
                    <a href="{{ route('downloads.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Start Download
                    </a>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Title</th>
                                    <th>Duration</th>
                                    <th>Size</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($downloads as $download)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-play-circle me-2 text-danger"></i>
                                            <div>
                                                <strong>{{ $download->title ?: 'Processing...' }}</strong>
                                                @if($download->video_id)
                                                    <br><small class="text-muted">ID: {{ $download->video_id }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $download->duration ?: '-' }}</td>
                                    <td>{{ $download->file_size ?: '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $download->status === 'completed' ? 'success' : 
                                            ($download->status === 'failed' ? 'danger' : 
                                            ($download->status === 'processing' ? 'primary' : 'secondary'))
                                        }}">
                                            {{ ucfirst($download->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $download->created_at->format('M j, H:i') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('downloads.show', $download) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($download->isCompleted())
                                                <a href="{{ route('downloads.download', $download) }}" 
                                                   class="btn btn-outline-success">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection