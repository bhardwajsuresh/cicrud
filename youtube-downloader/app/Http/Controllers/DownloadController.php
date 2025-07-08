<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Download;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = Download::orderBy('created_at', 'desc')->get();
        return view('downloads.index', compact('downloads'));
    }

    public function create()
    {
        return view('downloads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        // Extract video ID from URL
        $videoId = $this->extractVideoId($request->url);

        $download = Download::create([
            'url' => $request->url,
            'video_id' => $videoId,
            'status' => Download::STATUS_PENDING
        ]);

        // Process download in background
        $this->processDownload($download);

        return redirect()->route('downloads.show', $download)
            ->with('success', 'Download started! Please wait...');
    }

    public function show(Download $download)
    {
        return view('downloads.show', compact('download'));
    }

    public function download(Download $download)
    {
        if (!$download->isCompleted() || !$download->file_path) {
            return redirect()->back()->with('error', 'File not ready for download');
        }

        $filePath = storage_path('app/' . $download->file_path);
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found');
        }

        return response()->download($filePath, $download->title . '.mp4');
    }

    public function status(Download $download)
    {
        return response()->json([
            'status' => $download->status,
            'title' => $download->title,
            'duration' => $download->duration,
            'file_size' => $download->file_size,
            'error_message' => $download->error_message
        ]);
    }

    private function extractVideoId($url)
    {
        preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function processDownload(Download $download)
    {
        try {
            $download->update(['status' => Download::STATUS_PROCESSING]);

            // Create downloads directory
            $downloadsDir = storage_path('app/downloads');
            if (!file_exists($downloadsDir)) {
                mkdir($downloadsDir, 0755, true);
            }

            // Get video info first
            $infoCommand = sprintf(
                '%s/venv/bin/yt-dlp --print title --print duration --print filesize_approx "%s"',
                base_path(),
                escapeshellarg($download->url)
            );

            $infoResult = shell_exec($infoCommand);
            $infoLines = explode("\n", trim($infoResult));
            
            $title = $infoLines[0] ?? 'Unknown';
            $duration = $infoLines[1] ?? 'Unknown';
            $fileSize = $infoLines[2] ?? 'Unknown';

            $download->update([
                'title' => $title,
                'duration' => $duration,
                'file_size' => $fileSize
            ]);

            // Download the video
            $filename = Str::slug($title) . '_' . time() . '.%(ext)s';
            $downloadCommand = sprintf(
                '%s/venv/bin/yt-dlp -f "best[height<=720]" -o "%s/%s" "%s"',
                base_path(),
                $downloadsDir,
                $filename,
                escapeshellarg($download->url)
            );

            $output = shell_exec($downloadCommand . ' 2>&1');

            // Find the downloaded file
            $files = glob($downloadsDir . '/' . Str::slug($title) . '_' . time() . '.*');
            
            if (!empty($files)) {
                $filePath = 'downloads/' . basename($files[0]);
                $download->update([
                    'status' => Download::STATUS_COMPLETED,
                    'file_path' => $filePath,
                    'format' => pathinfo($files[0], PATHINFO_EXTENSION)
                ]);
            } else {
                throw new \Exception('Download failed: ' . $output);
            }

        } catch (\Exception $e) {
            $download->update([
                'status' => Download::STATUS_FAILED,
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
