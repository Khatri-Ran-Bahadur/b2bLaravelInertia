import CompanyDetails from '@/components/Company/CompanyDetails';
import CompanyDocuments from '@/components/Company/CompanyDocuments';
import CompanyProducts from '@/components/Company/CompanyProducts';
import CompanyReviews from '@/components/Company/CompanyReviews';
import CompanyTenders from '@/components/Company/CompanyTenders';
import AppLayout from '@/layouts/app-layout';
import { Company, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ArrowLeft, Building2 } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Companies',
        href: route('admin.companies.index'),
    },
    {
        title: 'Company Details',
        href: '',
    },
];

interface ShowProps {
    company: Company;
    activeTab: 'details' | 'products' | 'tenders' | 'documents' | 'reviews';
}

export default function Show({ company, activeTab }: ShowProps) {
    const { t } = useLaravelReactI18n();

    const tabs = [
        { id: 'details', label: 'Details', href: route('admin.companies.show', company.id) },
        { id: 'products', label: 'Products', href: route('admin.companies.products', company.id) },
        { id: 'tenders', label: 'Tenders', href: route('admin.companies.tenders', company.id) },
        { id: 'documents', label: 'Documents', href: route('admin.companies.documents', company.id) },
        { id: 'reviews', label: 'Reviews', href: route('admin.companies.reviews', company.id) },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${company.name} - Details`} />
            <div className="py-6">
                <div className="mx-auto rounded-lg bg-white shadow-sm dark:bg-gray-800">
                    <div className="p-6">
                        <div className="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <h1 className="mb-4 flex items-center text-2xl font-semibold text-gray-900 sm:mb-0 dark:text-white">
                                <Building2 className="mr-2 text-blue-600 dark:text-blue-400" size={24} />
                                {t('Company Details')}
                            </h1>
                            <Link
                                href={route('admin.companies.index')}
                                className="flex items-center text-gray-600 transition-colors duration-200 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                            >
                                <ArrowLeft size={16} className="mr-1" />
                                {t('Back to Companies')}
                            </Link>
                        </div>

                        {/* Tabs */}
                        <div className="mb-6 border-b border-gray-200 dark:border-gray-700">
                            <nav className="-mb-px flex space-x-8" aria-label="Tabs">
                                {tabs.map((tab) => (
                                    <Link
                                        key={tab.id}
                                        href={tab.href}
                                        className={`border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap ${
                                            activeTab === tab.id
                                                ? 'border-blue-500 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:border-gray-600 dark:hover:text-gray-300'
                                        }`}
                                    >
                                        {tab.label}
                                    </Link>
                                ))}
                            </nav>
                        </div>

                        {/* Tab Content */}
                        <div className="mt-6">
                            {activeTab === 'details' && <CompanyDetails company={company} />}
                            {activeTab === 'products' && <CompanyProducts company={company} />}
                            {activeTab === 'tenders' && <CompanyTenders company={company} />}
                            {activeTab === 'documents' && <CompanyDocuments company={company} />}
                            {activeTab === 'reviews' && <CompanyReviews company={company} />}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
