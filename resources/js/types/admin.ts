import type { UserRole } from './auth';

export type ManagedUser = {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    role: UserRole;
    email_verified_at: string | null;
    created_at: string | null;
    can_be_deleted: boolean;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type PaginatedResource<T> = {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        links: PaginationLink[];
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
};

export type UserFilters = {
    search: string;
    role: UserRole | '';
};
