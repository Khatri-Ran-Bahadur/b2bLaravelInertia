import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface ExtendedNavItem extends NavItem {
    submenus?: NavItem[];
}

export interface TenderCategory {
    id: number;
    name: string;
    slug: string;
    created_at: string;
    updated_at: string;
}

export interface Category{
    id: number;
    name: string;
    description: string;
    icon: string;
    image_url: string;
    parent_id: number | null;
    slug: string;
    created_at: string;
    updated_at: string;
}

export interface Company {
    id: number;
    name: string;
    email: string;
    phone: string;
    tin_number: string;
    owner: Owner;
    verification_status: string;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
}

export interface Tender {
    id: number;
    title: string;
    description: string;
    start_date: string;
    end_date: string;
    company_name: number;
    created_at: string;
    updated_at: string;
}

export interface Owner{
    id: number;
    name: string;
    email: string;
    phone: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    phone: string;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; 
}
