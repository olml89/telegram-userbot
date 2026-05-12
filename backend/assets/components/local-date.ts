export class LocalDate {
    private readonly date: Date;

    public constructor(iso8601Date: string) {
        this.date = new Date(iso8601Date);
    }

    public format(): string {
        return this.date.toLocaleString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    }

    public static from(iso8601Date: string|undefined): LocalDate|null {
        if (!iso8601Date) {
            return null;
        }

        return new LocalDate(iso8601Date);
    }
}
