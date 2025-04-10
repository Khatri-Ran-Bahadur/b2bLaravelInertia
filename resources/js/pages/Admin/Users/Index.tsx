import DataTable from '@/components/DataTable';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/react';
import { UserCheck } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Users',
        href: '/admin/users',
    },
];

export default function UserIndex() {
    const { users, filters, can } = usePage().props;

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
        { key: 'email', label: 'Email', sortable: true },
        { key: 'phone', label: 'Phone', sortable: true },
        { key: 'user_role', type: 'badge', label: 'Role', sortable: true },
        { key: 'created_at', type: 'date', label: 'Date', sortable: true },
    ];

    const handleDelete = (id: any) => {
        router.delete(route('admin.users.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log('User deleted successfully');
            },
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Users Management" />
            <div className="py-6">
                <div className="mx-auto">
                    <DataTable
                        data={users}
                        columns={columns}
                        resourceName="Users"
                        routeName="admin.users.index"
                        filters={filters}
                        canCreateResource={true}
                        canEditResource={false}
                        canDeleteResource={true}
                        icon={UserCheck}
                        createRoute="admin.users.create"
                        editRoute="admin.users.edit"
                        onDelete={handleDelete}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
