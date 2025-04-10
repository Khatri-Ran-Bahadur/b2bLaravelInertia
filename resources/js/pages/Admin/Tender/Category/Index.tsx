import DataTable from '@/components/DataTable';
import AppLayout from '@/layouts/app-layout';
import { TenderCategory, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Tender Categories',
        href: '',
    },
];

export default function Index({ tenderCategories }: { tenderCategories: TenderCategory[] }) {
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
        { key: 'name', label: 'Name', sortable: true },
    ];

    const handleDelete = (id: any) => {
        router.delete(route('admin.tender-categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {},
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tender Categories" />
            <div className="py-6">
                <div className="mx-auto">
                    {flash.success && <div className="mb-4 rounded-md bg-green-100 p-4 text-green-800">{flash.success}</div>}
                    <DataTable
                        data={tenderCategories}
                        columns={columns}
                        resourceName="Tender Categories"
                        singularName="Tender Category"
                        routeName="admin.tender-categories.index"
                        filters={filters}
                        canViewResource={false}
                        canCreateResource={true}
                        canEditResource={true}
                        canDeleteResource={true}
                        icon={Building2}
                        viewRoute={'admin.tender-categories.show'}
                        createRoute={'admin.tender-categories.create'}
                        editRoute={'admin.tender-categories.edit'}
                        deleteRoute={'admin.tender-categories.destroy'}
                        isCreateNew={true}
                        onDelete={handleDelete}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
