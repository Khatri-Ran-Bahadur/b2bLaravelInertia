import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Disclosure, DisclosureButton, DisclosurePanel, Transition } from '@headlessui/react';
import { Link, usePage } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ChevronRight } from 'lucide-react';
import { Fragment } from 'react';

// Extended NavItem type to include submenus
interface ExtendedNavItem extends NavItem {
    submenus?: NavItem[];
}

export function NavMain({ items = [] }: { items: ExtendedNavItem[] }) {
    const { t } = useLaravelReactI18n();
    const page = usePage();

    const isActive = (item: ExtendedNavItem) => {
        if (item.href === page.url) return true;

        // Check if any submenu is active
        if (item.submenus) {
            return item.submenus.some((submenu) => submenu.href === page.url);
        }

        return false;
    };

    return (
        <SidebarGroup className="px-2 py-0">
            <SidebarGroupLabel>{t('Sidebar')}</SidebarGroupLabel>
            <SidebarMenu>
                {items.map((item) => (
                    <div key={item.title} className="w-full">
                        {item.submenus ? (
                            // Parent menu with submenus using Disclosure
                            <Disclosure as="div" defaultOpen={isActive(item)}>
                                {({ open }) => (
                                    <>
                                        <SidebarMenuItem>
                                            <DisclosureButton as="div" className="w-full">
                                                <SidebarMenuButton
                                                    isActive={isActive(item)}
                                                    className="flex w-full justify-between transition-all duration-300"
                                                >
                                                    <div className="flex items-center">
                                                        {item.icon && <item.icon className="mr-2" />}
                                                        <span>{t(item.title)}</span>
                                                    </div>
                                                    <ChevronRight
                                                        size={16}
                                                        className={`transform transition-transform duration-300 ${open ? 'rotate-90' : ''}`}
                                                    />
                                                </SidebarMenuButton>
                                            </DisclosureButton>
                                        </SidebarMenuItem>

                                        <Transition
                                            as={Fragment}
                                            enter="transition-all duration-300 ease-out overflow-hidden"
                                            enterFrom="max-h-0 opacity-0"
                                            enterTo="max-h-96 opacity-100"
                                            leave="transition-all duration-200 ease-in overflow-hidden"
                                            leaveFrom="max-h-96 opacity-100"
                                            leaveTo="max-h-0 opacity-0"
                                        >
                                            <DisclosurePanel className="overflow-hidden pl-6">
                                                {item.submenus?.map((submenu, index) => (
                                                    <div
                                                        key={item.title + submenu.title}
                                                        className="transition-all duration-300"
                                                        style={{
                                                            transitionDelay: `${index * 50}ms`,
                                                            transform: open ? 'translateX(0)' : 'translateX(-10px)',
                                                            opacity: open ? 1 : 0,
                                                        }}
                                                    >
                                                        <SidebarMenuItem>
                                                            <SidebarMenuButton asChild isActive={submenu.href === page.url}>
                                                                <Link href={submenu.href} prefetch className="transition-colors duration-200">
                                                                    <span>{t(submenu.title)}</span>
                                                                </Link>
                                                            </SidebarMenuButton>
                                                        </SidebarMenuItem>
                                                    </div>
                                                ))}
                                            </DisclosurePanel>
                                        </Transition>
                                    </>
                                )}
                            </Disclosure>
                        ) : (
                            // Regular menu item without submenus
                            <SidebarMenuItem>
                                <SidebarMenuButton asChild isActive={item.href === page.url}>
                                    <Link href={item.href} prefetch className="transition-colors duration-200">
                                        {item.icon && <item.icon className="mr-2" />}
                                        <span>{t(item.title)}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        )}
                    </div>
                ))}
            </SidebarMenu>
        </SidebarGroup>
    );
}
