export interface File {
    id: number;
    name: string;
    path: string;
    fileable_id: number;
    fileable_type: string;
    type: string;
    size: number;
    created_at?: string;
    updated_at?: string;
}

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    file?: File | null;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};
