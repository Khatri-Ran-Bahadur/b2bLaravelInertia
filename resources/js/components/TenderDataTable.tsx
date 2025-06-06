// DataTable.jsx
import { router, usePage } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ArrowDown, ArrowUp, ChevronLeft, ChevronRight, Search } from 'lucide-react';
import { useState } from 'react';
import DeleteDialog from './DeleteDialog';

interface DataTableProps {
    data: any;
    columns: any[];
    resourceName: string;
    routeName: string;
    filters: any;
    viewRoute: string;
    canViewResource: boolean;
    canCreateResource: boolean;
    canEditResource: boolean;
    canDeleteResource: boolean;
    icon: any;
    createRoute: string;
    editRoute: string;
    onDelete: (id: number) => void;
    isCreateNew: boolean;
    singularName: string;
}

interface TableColumn {
    key: string;
    label: string;
    type: string;
    sortable: boolean;
    width?: string;
}

export default function DataTable({
    data,
    columns = [],
    resourceName = '',
    routeName = '',
    filters = {},
    viewRoute = '',
    canViewResource = false,
    canCreateResource = false,
    canEditResource = false,
    canDeleteResource = false,
    icon: Icon,
    createRoute = '',
    editRoute = '',
    onDelete,
    isCreateNew = false,
    singularName = '',
}: DataTableProps) {
    const { errors, companies, tenderCategories } = usePage().props;

    const { t } = useLaravelReactI18n();

    // Initialize state with filters from props
    const [search, setSearch] = useState(filters?.search || '');
    const [perPage, setPerPage] = useState(filters?.perPage || 10);
    const [sort, setSort] = useState(filters?.sort || 'id');
    const [direction, setDirection] = useState(filters?.direction || 'desc');
    const [company_id, setCompanyId] = useState(filters?.company_id || '');
    const [tender_category_id, setTenderCategoryId] = useState(filters?.tender_category_id || '');

    const [itemToDelete, setItemToDelete] = useState(null);
    const [showDeleteDialog, setShowDeleteDialog] = useState(false);

    // Update route params with current filters
    const updateRoute = (newParams = {}) => {
        const params = {
            search,
            perPage,
            sort,
            direction,
            page: 1,
            ...newParams,
        };

        router.get(route(routeName), params, {
            preserveState: true,
            preserveScroll: true,
            // only: ['data', 'filters'],
        });
    };

    // Handle search
    const handleSearch = (e: any) => {
        e.preventDefault();
        updateRoute();
    };

    // Handle per-page change
    const handlePerPageChange = (e: any) => {
        const newPerPage = e.target.value;
        setPerPage(newPerPage);
        updateRoute({ perPage: newPerPage });
    };

    const handleCompanySelect = (e: any) => {
        const company_id = e.target.value;
        setCompanyId(company_id);
        updateRoute({ company_id: company_id });
    };

    const handleCategorySelect = (e: any) => {
        const tender_category_id = e.target.value;
        setTenderCategoryId(e.target.value);
        updateRoute({ tender_category_id: tender_category_id });
    };

    // Handle sorting
    const handleSort = (column: string) => {
        const newDirection = sort === column && direction === 'asc' ? 'desc' : 'asc';
        setSort(column);
        setDirection(newDirection);
        updateRoute({ sort: column, direction: newDirection });
    };

    // Format date nicely
    const formatDate = (dateString: any) => {
        const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    const formatDate2 = (dateString: any) => {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is zero-based
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    // Render cell content based on column type
    const renderCell = (item: any, column: any, index: number) => {
        if (!column.key) return null;

        // Get value from item using dot notation for nested properties
        const getValue = (obj: any, path: string) => {
            return path.split('.').reduce((acc, part) => acc && acc[part], obj);
        };

        const value = getValue(item, column.key);

        if (column.type === 'date' && value) {
            return formatDate(value);
        }

        if (column.type === 'date2' && value) {
            return formatDate2(value);
        }

        if (column.type === 'badge') {
            return (
                <span className="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {value}
                </span>
            );
        }
        if (column.type === 'boolean') {
            return value ? (
                <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                    {t('Yes')}
                </span>
            ) : (
                <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                    {t('No')}
                </span>
            );
        }
        if (column.type === 'custom' && column.render) {
            return column.render(item);
        }
        if (column.type === 'IndexColumn' && column.render) {
            return column.render(item, index);
        }

        return value;
    };

    // Action buttons column
    const renderActions = (item: any) => {
        return (
            <div className="flex space-x-2">
                {canViewResource && (
                    <button
                        onClick={() => router.visit(route(viewRoute, item.id))}
                        className="rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800"
                    >
                        {t('View')}
                    </button>
                )}
                {canEditResource && (
                    <button
                        onClick={() => router.visit(route(editRoute, item.id))}
                        className="rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-100 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800"
                    >
                        {t('Edit')}
                    </button>
                )}
                {canDeleteResource && (
                    <button
                        onClick={() => {
                            setItemToDelete(item);
                            setShowDeleteDialog(true);
                        }}
                        className="rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800 dark:focus:ring-red-400 dark:focus:ring-offset-gray-800"
                    >
                        {t('Delete')}
                    </button>
                )}
            </div>
        );
    };

    singularName = singularName ? singularName : resourceName.endsWith('s') ? resourceName.slice(0, -1) : resourceName;

    // Add actions column if needed

    let tableColumns: TableColumn[] = [...columns];
    if (canViewResource || canEditResource || canDeleteResource) {
        tableColumns.push({
            key: 'actions',
            label: 'Actions',
            type: 'custom',
            sortable: false,
            render: renderActions,
        });
    }

    return (
        <div className="w-full bg-white dark:bg-gray-800">
            {/* Header and Controls */}
            <div className="px-6 py-4">
                <div className="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                    <div className="flex items-center">
                        {Icon && <Icon className="mr-3 h-6 w-6 text-blue-600 dark:text-blue-400" />}
                        <h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">{t(resourceName)}</h2>
                    </div>
                    {isCreateNew && (
                        <a
                            href={route(createRoute)}
                            className="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none dark:hover:bg-blue-500 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800"
                        >
                            {t('Add')} {singularName}
                        </a>
                    )}
                </div>

                {/* Search and Per Page Controls */}
                <div className="mb-6 flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
                    <form onSubmit={handleSearch} className="relative flex w-full max-w-md">
                        <input
                            type="text"
                            placeholder={t(`Search`) + `...`}
                            className="w-full rounded-lg border border-gray-300 bg-white py-2 pr-4 pl-10 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-800"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                        <Search className="absolute top-2.5 left-3 h-4 w-4 text-gray-400 dark:text-gray-500" />
                        <button
                            type="submit"
                            className="ml-2 rounded-lg bg-blue-600 px-4 py-2 text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 focus:outline-none dark:hover:bg-blue-500 dark:focus:ring-blue-400 dark:focus:ring-offset-gray-800"
                        >
                            {t('Search')}
                        </button>
                    </form>
                    <div className="flex items-center space-x-4">
                        <div>
                            <label htmlFor="company" className="mr-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                                {t('Company')}:
                            </label>
                            <select
                                id="company"
                                className="rounded-lg border border-gray-300 bg-white py-2 pr-8 pl-3 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-800"
                                value={company_id}
                                onChange={handleCompanySelect}
                            >
                                <option value="">{t('All')}</option>
                                {companies
                                    ? companies.map((company: any) => (
                                          <option key={company.id} value={company.id}>
                                              {company.name}
                                          </option>
                                      ))
                                    : null}
                            </select>
                        </div>

                        <div>
                            <label htmlFor="tenderCategories" className="mr-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                                {t('Categories')}:
                            </label>
                            <select
                                id="tenderCategories"
                                className="rounded-lg border border-gray-300 bg-white py-2 pr-8 pl-3 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-800"
                                value={tender_category_id}
                                onChange={handleCategorySelect}
                            >
                                <option value="">{t('All')}</option>
                                {tenderCategories
                                    ? tenderCategories.map((category) => (
                                          <option key={category.id} value={category.id}>
                                              {category.name}
                                          </option>
                                      ))
                                    : null}
                            </select>
                        </div>
                    </div>

                    <div className="flex items-center">
                        <label htmlFor="perPage" className="mr-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                            {t('Show')}:
                        </label>
                        <select
                            id="perPage"
                            className="rounded-lg border border-gray-300 bg-white py-2 pr-8 pl-3 text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:focus:border-blue-400 dark:focus:ring-blue-800"
                            value={perPage}
                            onChange={handlePerPageChange}
                        >
                            <option value="5">{t('5 per page')}</option>
                            <option value="10">{t('10 per page')}</option>
                            <option value="25">{t('25 per page')}</option>
                            <option value="50">{t('50 per page')}</option>
                            <option value="100">{t('100 per page')}</option>
                        </select>
                    </div>
                </div>

                {/* Data Table */}
                <div className="overflow-hidden rounded-lg border border-gray-200 shadow dark:border-gray-700">
                    <table className="min-w-full divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        <thead>
                            <tr className="bg-gray-50 dark:bg-gray-700">
                                {tableColumns.map((column) => (
                                    <th
                                        key={column.key}
                                        className="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                        style={column.width ? { width: column.width } : {}}
                                    >
                                        {column.sortable !== false ? (
                                            <button className="group inline-flex items-center" onClick={() => handleSort(column.key)}>
                                                {t(column.label)}
                                                <span className="ml-2">
                                                    {sort === column.key ? (
                                                        direction === 'asc' ? (
                                                            <ArrowUp className="h-4 w-4 text-blue-500 dark:text-blue-400" />
                                                        ) : (
                                                            <ArrowDown className="h-4 w-4 text-blue-500 dark:text-blue-400" />
                                                        )
                                                    ) : (
                                                        <span className="opacity-0 group-hover:opacity-50">
                                                            <ArrowUp className="h-4 w-4" />
                                                        </span>
                                                    )}
                                                </span>
                                            </button>
                                        ) : (
                                            t(column.label)
                                        )}
                                    </th>
                                ))}
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                            {data.data.length > 0 ? (
                                data.data.map((item: any, index: number) => (
                                    <tr key={item.id} className="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {tableColumns.map((column) => (
                                            <td
                                                key={`${item.id}-${column.key}`}
                                                className="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400"
                                            >
                                                {renderCell(item, column, index)}
                                            </td>
                                        ))}
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan={tableColumns.length} className="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div className="flex flex-col items-center justify-center">
                                            {Icon && <Icon className="mb-2 h-10 w-10 text-gray-400 dark:text-gray-500" />}
                                            <p className="font-medium">
                                                {t('No')} {resourceName.toLowerCase()} {t('found')}
                                            </p>
                                            <p className="mt-1 text-gray-400 dark:text-gray-500">{t('Try adjusting your search criteria')}</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div className="mt-6 flex items-center justify-between">
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                        {t('Showing')} <span className="font-medium">{data.from || 0}</span> to <span className="font-medium">{data.to || 0}</span>{' '}
                        {t('of')}
                        <span className="font-medium">{data.total}</span> {t('results')}
                    </p>

                    <div className="flex items-center space-x-1">
                        <button
                            onClick={() => data.prev_page_url && router.visit(data.prev_page_url)}
                            disabled={!data.prev_page_url}
                            className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        >
                            <ChevronLeft className="h-4 w-4" />
                        </button>

                        {data.links &&
                            data.links.map((link, index) => {
                                // Skip "prev" and "next" buttons
                                if (link.label.includes('Previous') || link.label.includes('Next')) {
                                    return null;
                                }

                                // Try to parse the label as a number
                                const pageNum = parseInt(link.label);
                                if (isNaN(pageNum) && link.label.includes('...')) {
                                    return (
                                        <span
                                            key={index}
                                            className="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                        >
                                            ...
                                        </span>
                                    );
                                }

                                return (
                                    <button
                                        key={index}
                                        className={`relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium ${
                                            link.active
                                                ? 'bg-blue-600 text-white dark:bg-blue-500'
                                                : 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600'
                                        }`}
                                        onClick={() => router.visit(link.url)}
                                        disabled={!link.url}
                                    >
                                        {pageNum || link.label}
                                    </button>
                                );
                            })}

                        <button
                            onClick={() => data.next_page_url && router.visit(data.next_page_url)}
                            disabled={!data.next_page_url}
                            className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        >
                            <ChevronRight className="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>

            <DeleteDialog
                isOpen={showDeleteDialog}
                onClose={() => setShowDeleteDialog(false)}
                onConfirm={() => onDelete(itemToDelete?.id)}
                message={t('delete_confirmation', { name: singularName.toLowerCase() })}
            />
        </div>
    );
}
