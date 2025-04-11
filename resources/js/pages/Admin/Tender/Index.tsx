import DataTable from '@/components/TenderDataTable';
import AppLayout from '@/layouts/app-layout';
import { Tender, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Tenders',
        href: '',
    },
];

export default function Index({ tenders }: { tenders: Tender[] }) {
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
        { key: 'title', label: 'Title', sortable: true },
        { key: 'budget_from', label: 'Budget From', type: 'number', sortable: true },
        { key: 'budget_to', label: 'Budget To', type: 'number', sortable: true },
        { key: 'phone', label: 'Phone', sortable: true },
        { key: 'active_status', label: 'Active Status', sortable: true },
        { key: 'created_at', label: 'Date', type: 'date', sortable: true },
    ];

    const handleDelete = (id: any) => {
        router.delete(route('admin.tenders.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {},
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tenders" />
            <div className="py-6">
                <div className="mx-auto">
                    {flash.success && <div className="mb-4 rounded-md bg-green-100 p-4 text-green-800">{flash.success}</div>}
                    <DataTable
                        data={tenders}
                        columns={columns}
                        resourceName="Tenders"
                        singularName="Tender"
                        filters={filters}
                        canViewResource={true}
                        canCreateResource={true}
                        canEditResource={false}
                        canDeleteResource={false}
                        icon={Building2}
                        routeName="admin.tenders.index"
                        viewRoute={'admin.tenders.show'}
                        createRoute=""
                        deleteRoute=""
                        isCreateNew={false}
                        onDelete={handleDelete}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
