@extends('layouts.app')

@section('title', 'New Download')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Download YouTube Video</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('downloads.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="url" class="form-label">YouTube URL</label>
                        <input type="url" 
                               class="form-control @error('url') is-invalid @enderror" 
                               id="url" 
                               name="url" 
                               value="{{ old('url') }}"
                               placeholder="https://www.youtube.com/watch?v=..."
                               required>
                        @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Paste the YouTube video URL you want to download
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> 
                        Videos will be downloaded in 720p quality or lower to ensure faster downloads and compatibility.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('downloads.index') }}" class="btn btn-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-download"></i> Start Download
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-question-circle"></i> Supported URLs</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-check-circle text-success"></i> YouTube videos (youtube.com/watch?v=...)</li>
                    <li><i class="bi bi-check-circle text-success"></i> YouTube short URLs (youtu.be/...)</li>
                    <li><i class="bi bi-check-circle text-success"></i> YouTube live streams</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection