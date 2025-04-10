import DataTable from '@/components/DataTable';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { Building2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Companies',
        href: '',
    },
];

export default function Index() {
    const { companies, filters, can } = usePage().props;

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
        { key: 'tin_number', label: 'Tin Number', sortable: true },
        { key: 'phone', label: 'Phone', sortable: true },
        { key: 'created_at', type: 'date', label: 'Date', sortable: true },
    ];

    const handleDelete = (id: any) => {
        router.delete(route('admin.companies.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('User deleted successfully');
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Company Management" />
            <div className="py-6">
                <div className="mx-auto">
                    <DataTable
                        data={companies}
                        columns={columns}
                        resourceName="Companies"
                        routeName="admin.companies.index"
                        filters={filters}
                        canViewResource={true}
                        canCreateResource={false}
                        canEditResource={false}
                        canDeleteResource={true}
                        icon={Building2}
                        viewRoute={'admin.companies.show'}
                        onDelete={handleDelete}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
