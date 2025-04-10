import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { ExtendedNavItem, type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Building2, Contact2Icon, DiamondIcon, LayoutGrid, ListIcon, TentTreeIcon, Users } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: ExtendedNavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Users',
        href: '/admin/users',
        icon: Users,
    },
    {
        title: 'Companies',
        href: '/admin/companies',
        icon: Building2,
    },
    {
        title: 'Tenders',
        icon: TentTreeIcon,
        href: '#', // Placeholder or can be set to the main tenders page
        submenus: [
            {
                title: 'Categories',
                href: '/admin/tender-categories',
            },
            {
                title: 'Tenders',
                href: '/admin/tenders',
            },
        ],
    },
    {
        title: 'Contracts',
        href: '/admin/contracts',
        icon: Contact2Icon,
    },
    {
        title: 'Products',
        icon: DiamondIcon,
        href: '#', // Placeholder
        submenus: [
            {
                title: 'Products',
                href: '/admin/products',
                icon: DiamondIcon,
            },
            {
                title: 'Categories',
                href: '/admin/categories',
                icon: ListIcon,
            },
        ],
    },
];

const footerNavItems: NavItem[] = [];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
