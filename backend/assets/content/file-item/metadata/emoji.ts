export interface EmojiProvider {
    emoji(): string;
}

export class FileEmojiProvider implements EmojiProvider {
    public emoji(): string {
        return '📦';
    }
}

export class DocumentEmojiProvider implements EmojiProvider {
    public emoji(): string {
        return '📄';
    }
}

export class ImageEmojiProvider implements EmojiProvider {
    public emoji(): string {
        return '🖼️';
    }
}

export class VideoEmojiProvider implements EmojiProvider {
    public emoji(): string {
        return '📹';
    }
}

export class AudioEmojiProvider implements EmojiProvider {
    public emoji(): string {
        return '🎵';
    }
}
