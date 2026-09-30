import { usePage } from '@inertiajs/vue3';
import ptBR from '../../../lang/pt_BR.json';

type TranslationReplacements = Record<string, string | number>;

const translationsByLocale: Record<string, Record<string, string>> = {
    pt_BR: ptBR,
};

export function trans(
    key: string,
    replacements: TranslationReplacements = {},
): string {
    const locale = usePage().props.locale;
    const translation = translationsByLocale[locale]?.[key] ?? key;

    return Object.entries(replacements)
        .sort(
            ([firstName], [secondName]) => secondName.length - firstName.length,
        )
        .reduce(
            (text, [name, value]) => text.replaceAll(`:${name}`, String(value)),
            translation,
        );
}

export function currentLanguageTag(): string {
    return usePage().props.locale.replace('_', '-');
}
