export function capitalize(text: string): string {
    return text.charAt(0).toUpperCase() + text.slice(1);
}

export function pluralize(text: string, count?: number): string {
    return count === 1 ? text : `${text}s`;
}

export function toNumber(value: string): number|null {
    const trimmed = value.trim();

    if (trimmed.length === 0) {
        return null;
    }

    return Number.isNaN(Number(trimmed)) ? null : Number(trimmed);
}
