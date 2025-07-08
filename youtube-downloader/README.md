# YouTube Live Video Downloader

A Laravel-based web application for downloading YouTube videos using yt-dlp and FFmpeg. This is a proof of concept project that provides a clean web interface for downloading YouTube videos with real-time status updates.

## Features

- **Web Interface**: Clean Bootstrap-based UI for easy video downloading
- **Real-time Updates**: Live status updates during download process
- **Video Information**: Extracts title, duration, and file size
- **Download Management**: Track all downloads with status indicators
- **Quality Control**: Downloads videos in 720p or lower for optimal performance
- **Error Handling**: Comprehensive error reporting and status tracking

## Technology Stack

- **Backend**: Laravel PHP Framework
- **Frontend**: Bootstrap 5 with Bootstrap Icons
- **Video Processing**: yt-dlp (YouTube downloader)
- **Database**: SQLite (for proof of concept)
- **Server Requirements**: PHP 8.1+, FFmpeg

## Installation

### Prerequisites

Make sure you have the following installed:
- PHP 8.1 or higher
- Composer
- FFmpeg
- Python 3 with pip
- SQLite support for PHP

### Setup Steps

1. **Clone and Install Dependencies**
   ```bash
   git clone <your-repo>
   cd youtube-downloader
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate
   ```

4. **Install yt-dlp**
   ```bash
   python3 -m venv venv
   venv/bin/pip install yt-dlp
   ```

5. **Create Storage Directory**
   ```bash
   mkdir -p storage/app/downloads
   chmod 755 storage/app/downloads
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Usage

1. **Access the Application**
   - Open your browser and go to `http://localhost:8000`

2. **Download a Video**
   - Click "New Download"
   - Paste a YouTube URL (supports youtube.com/watch?v= and youtu.be/ formats)
   - Click "Start Download"

3. **Monitor Progress**
   - The status page will automatically update every 3 seconds
   - View download progress in real-time
   - Download the file once completed

4. **Manage Downloads**
   - View all downloads in the main dashboard
   - Check status, file size, and duration
   - Download completed files

## Supported URLs

- YouTube videos: `https://www.youtube.com/watch?v=VIDEO_ID`
- YouTube short URLs: `https://youtu.be/VIDEO_ID`
- YouTube live streams (when available)

## Download Process

1. **URL Validation**: Validates YouTube URL format
2. **Video Information**: Extracts metadata using yt-dlp
3. **Download**: Downloads video in 720p or best available quality
4. **Storage**: Saves file in `storage/app/downloads/`
5. **Completion**: Provides download link for completed files

## Database Schema

The application uses a simple `downloads` table with the following fields:

- `id`: Primary key
- `url`: YouTube video URL
- `video_id`: Extracted YouTube video ID
- `title`: Video title
- `duration`: Video duration
- `status`: Download status (pending, processing, completed, failed)
- `file_path`: Path to downloaded file
- `file_size`: Approximate file size
- `format`: Video format/extension
- `error_message`: Error details (if failed)
- `metadata`: JSON field for additional data
- `created_at/updated_at`: Timestamps

## API Endpoints

### Web Routes
- `GET /` - Main downloads list
- `GET /downloads/create` - New download form
- `POST /downloads` - Create new download
- `GET /downloads/{id}` - View download details
- `GET /downloads/{id}/download` - Download file
- `GET /downloads/{id}/status` - Get download status (JSON)

## Configuration

### Video Quality
The application downloads videos in 720p or lower by default. To change this, modify the format selector in `DownloadController.php`:

```php
$downloadCommand = sprintf(
    '%s/venv/bin/yt-dlp -f "best[height<=1080]" -o "%s/%s" "%s"',
    // ... rest of command
);
```

### Download Directory
Downloaded files are stored in `storage/app/downloads/`. Ensure this directory has proper write permissions.

## Security Considerations

⚠️ **Important**: This is a proof of concept application. For production use, consider:

- Input sanitization and validation
- Rate limiting for downloads
- User authentication and authorization
- File storage optimization
- Background job processing for large downloads
- Disk space monitoring
- Content filtering and compliance

## Troubleshooting

### Common Issues

1. **yt-dlp not found**
   - Ensure Python virtual environment is created and yt-dlp is installed
   - Check the path in `processDownload()` method

2. **Permission denied on downloads directory**
   ```bash
   chmod -R 755 storage/app/downloads
   ```

3. **FFmpeg not found**
   ```bash
   sudo apt-get install ffmpeg  # Ubuntu/Debian
   brew install ffmpeg          # macOS
   ```

4. **PHP SQLite extension missing**
   ```bash
   sudo apt-get install php-sqlite3
   ```

## Development

### Adding New Features

1. **Queue Support**: Implement Laravel queues for background processing
2. **Progress Tracking**: Add real-time download progress
3. **Playlist Support**: Enable downloading entire playlists
4. **Format Selection**: Allow users to choose video quality/format
5. **User Management**: Add authentication and user-specific downloads

### File Structure

```
youtube-downloader/
├── app/
│   ├── Http/Controllers/
│   │   └── DownloadController.php
│   └── Models/
│       └── Download.php
├── database/migrations/
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   └── downloads/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── show.blade.php
├── storage/app/downloads/
├── venv/ (Python virtual environment)
└── routes/web.php
```

## License

This project is for educational and proof of concept purposes. Please ensure compliance with YouTube's Terms of Service and applicable laws when using this application.

## Contributing

This is a proof of concept project. For suggestions or improvements, please consider:

1. Error handling enhancements
2. UI/UX improvements
3. Performance optimizations
4. Additional video platform support
5. Mobile responsiveness improvements
