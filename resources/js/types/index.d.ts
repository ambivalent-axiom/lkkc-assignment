import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
}

export interface Farm {
    id: number;
    user_id: number;
    name: string;
    email: string;
    website: string;
    animals: Animal[];
}

export interface Animal {
    id: number;
    user_id: number;
    farm_id: number;
    animal_number: number;
    type_name: string;
    years: number;
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
    farms: {
        data: Farm[];
        links: string[];
    }
    animals: Animal[];
};
