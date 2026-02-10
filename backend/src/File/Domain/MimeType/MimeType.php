<?php

declare(strict_types=1);

namespace olml89\TelegramUserbot\Backend\File\Domain\MimeType;

enum MimeType: string
{
    // Image
    case jpeg = 'image/jpeg';
    case png = 'image/png';
    case gif = 'image/gif';
    case webp = 'image/webp';
    case bmp = 'image/bmp';
    case svg = 'image/svg+xml';
    case tiff = 'image/tiff';

    // Audio
    case mp3 = 'audio/mpeg';
    case wav = 'audio/wav';
    case ogg_audio = 'audio/ogg';
    case aac = 'audio/aac';
    case webm_audio = 'audio/webm';
    case flac = 'audio/flac';
    case m4a = 'audio/mp4';

    // Video
    case mp4 = 'video/mp4';
    case mpeg = 'video/mpeg';
    case webm_video = 'video/webm';
    case ogg_video = 'video/ogg';
    case avi = 'video/x-msvideo';
    case mkv = 'video/x-matroska';
    case mov = 'video/quicktime';
    case gpp3 = 'video/3gpp';
    case gpp32 = 'video/3gpp2';

    // Document
    case pdf = 'application/pdf';
    case txt = 'text/plain';
}
