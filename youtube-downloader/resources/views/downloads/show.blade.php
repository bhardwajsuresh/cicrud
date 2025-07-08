@extends('layouts.app')

@section('title', 'Download Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-info-circle"></i> Download Details</h4>
                <span class="badge bg-{{ 
                    $download->status === 'completed' ? 'success' : 
                    ($download->status === 'failed' ? 'danger' : 
                    ($download->status === 'processing' ? 'primary' : 'secondary'))
                }}" id="status-badge">
                    {{ ucfirst($download->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3"><strong>URL:</strong></div>
                    <div class="col-sm-9">
                        <a href="{{ $download->url }}" target="_blank" class="text-break">
                            {{ $download->url }}
                        </a>
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>Title:</strong></div>
                    <div class="col-sm-9" id="title">
                        {{ $download->title ?: 'Loading...' }}
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>Video ID:</strong></div>
                    <div class="col-sm-9">{{ $download->video_id ?: 'N/A' }}</div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>Duration:</strong></div>
                    <div class="col-sm-9" id="duration">
                        {{ $download->duration ?: 'Loading...' }}
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>File Size:</strong></div>
                    <div class="col-sm-9" id="file_size">
                        {{ $download->file_size ?: 'Loading...' }}
                    </div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>Format:</strong></div>
                    <div class="col-sm-9">{{ $download->format ?: 'N/A' }}</div>
                </div>
                <hr>
                
                <div class="row">
                    <div class="col-sm-3"><strong>Created:</strong></div>
                    <div class="col-sm-9">{{ $download->created_at->format('F j, Y \a\t H:i') }}</div>
                </div>

                @if($download->error_message)
                <hr>
                <div class="row">
                    <div class="col-sm-3"><strong>Error:</strong></div>
                    <div class="col-sm-9">
                        <div class="alert alert-danger mb-0">
                            {{ $download->error_message }}
                        </div>
                    </div>
                </div>
                @endif

                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('downloads.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Downloads
                    </a>
                    
                    @if($download->isCompleted())
                        <a href="{{ route('downloads.download', $download) }}" 
                           class="btn btn-success" id="download-btn">
                            <i class="bi bi-download"></i> Download File
                        </a>
                    @elseif($download->isProcessing())
                        <button class="btn btn-primary" disabled>
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Processing...
                        </button>
                    @elseif($download->isFailed())
                        <button class="btn btn-danger" disabled>
                            <i class="bi bi-x-circle"></i> Failed
                        </button>
                    @else
                        <button class="btn btn-secondary" disabled>
                            <i class="bi bi-clock"></i> Pending
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let statusPoll;

function updateStatus() {
    fetch('{{ route("downloads.status", $download) }}')
        .then(response => response.json())
        .then(data => {
            // Update status badge
            const statusBadge = document.getElementById('status-badge');
            statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            statusBadge.className = 'badge bg-' + 
                (data.status === 'completed' ? 'success' : 
                (data.status === 'failed' ? 'danger' : 
                (data.status === 'processing' ? 'primary' : 'secondary')));

            // Update details
            if (data.title) {
                document.getElementById('title').textContent = data.title;
            }
            if (data.duration) {
                document.getElementById('duration').textContent = data.duration;
            }
            if (data.file_size) {
                document.getElementById('file_size').textContent = data.file_size;
            }

            // Handle completion
            if (data.status === 'completed') {
                clearInterval(statusPoll);
                location.reload(); // Reload to show download button
            }
            
            // Handle failure
            if (data.status === 'failed') {
                clearInterval(statusPoll);
                if (data.error_message) {
                    location.reload(); // Reload to show error message
                }
            }
        })
        .catch(error => {
            console.error('Error fetching status:', error);
        });
}

// Start polling if not completed or failed
@if($download->isProcessing() || $download->status === 'pending')
    statusPoll = setInterval(updateStatus, 3000); // Poll every 3 seconds

    // Clean up on page unload
    window.addEventListener('beforeunload', function() {
        if (statusPoll) {
            clearInterval(statusPoll);
        }
    });
@endif
</script>
@endsection