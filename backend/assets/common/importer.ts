import { Component } from './component/contracts';

type QueryResult =
    | Element
    | NodeListOf<any>
    | Array<Element|HTMLElement>
    | Component
    | null
    | boolean
    | undefined;

export type Imported<T> = {
    [K in keyof T]-?: Exclude<T[K], null|undefined|false>;
};

export const assertImported = <T extends Record<string, QueryResult>>(
    name: string,
    components: T,
): components is Imported<T> => {
    let importedComponents: Record<string, boolean> = {};

    (Object.entries(components) as [keyof T, T[keyof T]][]).forEach(([key, value]) => {
        importedComponents[key as string] = Boolean(value);
    });

    if (!Object.values(importedComponents).every(Boolean)) {
        console.warn(`[${name}] Missing required elements/components`, importedComponents);

        return false;
    }

    return true;
}

export const querySelector = <T extends Element>(
    root: Document|ParentNode|null,
    selector: string,
): T | null => (root ? root.querySelector<T>(selector) : null);

export const querySelectorAll = <T extends Element>(
    root: Document|ParentNode|null,
    selector: string,
): NodeListOf<T> => (root ? root.querySelectorAll<T>(selector) : ([] as unknown as NodeListOf<T>));
