import DataTable from '@/components/DataTable';
import AppLayout from '@/layouts/app-layout';
import { Category, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Categories',
        href: '',
    },
];

export default function Index({ categories }: { categories: Category[] }) {
    const { filters, can, flash } = usePage().props;
    const columns = [
        {
            key: 'index',
            label: '#',
            type: 'IndexColumn',
            width: '80px',
            sortable: false,
            render: (item: any, index: number) => {
                return (filters.page - 1) * filters.perPage + index + 1;
            },
        },
        { key: 'image', label: 'Image', type: 'image', design: 'rec', sortable: true },
        { key: 'name', label: 'Name', sortable: true },
        { key: 'slug', label: 'Slug', sortable: true },
        { key: 'parent_name', label: 'Parent Name', sortable: true },
    ];

    const handleDelete = (id: any) => {
        router.delete(route('admin.categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {},
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Categories" />
            <div className="py-6">
                <div className="mx-auto">
                    {flash.success && <div className="mb-4 rounded-md bg-green-100 p-4 text-green-800">{flash.success}</div>}
                    <DataTable
                        data={categories}
                        columns={columns}
                        resourceName="Categories"
                        singularName="Category"
                        routeName="admin.categories.index"
                        filters={filters}
                        canViewResource={false}
                        canCreateResource={true}
                        canEditResource={true}
                        canDeleteResource={true}
                        icon={Building2}
                        viewRoute={'admin.categories.show'}
                        createRoute={'admin.categories.create'}
                        editRoute={'admin.categories.edit'}
                        deleteRoute={'admin.categories.destroy'}
                        isCreateNew={true}
                        onDelete={handleDelete}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
