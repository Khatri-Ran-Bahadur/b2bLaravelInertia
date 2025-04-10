import AppLayout from '@/layouts/app-layout';
import { Company, type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ArrowLeft, Building2, Calendar, CheckCircle, FileText, Phone, User, XCircle } from 'lucide-react';

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

export default function Show({ company }: { company: Company }) {
    const { t } = useLaravelReactI18n();
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${company.name} - Details`} />
            <div className="py-6">
                <div className="mx-auto rounded-lg bg-white shadow-sm">
                    <div className="p-6">
                        <div className="mb-6 flex items-center justify-between">
                            <h1 className="flex items-center text-2xl font-semibold">
                                <Building2 className="mr-2" size={24} />
                                {t('Company Details')}
                            </h1>
                            <Link href={route('admin.companies.index')} className="flex items-center text-gray-600 hover:text-gray-900">
                                <ArrowLeft size={16} className="mr-1" />
                                {t('Back to Companies')}
                            </Link>
                        </div>

                        <div className="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div className="rounded-lg bg-gray-50 p-4">
                                <h2 className="mb-4 text-lg font-medium">{t('Basic Information')}</h2>
                                <div className="space-y-3">
                                    <div className="flex items-start">
                                        <Building2 size={18} className="mt-1 mr-2 text-gray-500" />
                                        <div>
                                            <p className="text-sm text-gray-500">{t('Company Name')}</p>
                                            <p className="font-medium">{company.name}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start">
                                        <FileText size={18} className="mt-1 mr-2 text-gray-500" />
                                        <div>
                                            <p className="text-sm text-gray-500">{t('TIN Number')}</p>
                                            <p className="font-medium">{company.tin_number}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start">
                                        <Phone size={18} className="mt-1 mr-2 text-gray-500" />
                                        <div>
                                            <p className="text-sm text-gray-500">{t('Phone Number')}</p>
                                            <p className="font-medium">{company.phone}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="rounded-lg bg-gray-50 p-4">
                                <h2 className="mb-4 text-lg font-medium">{t('Additional Details')}</h2>
                                <div className="space-y-3">
                                    <div className="flex items-start">
                                        <User size={18} className="mt-1 mr-2 text-gray-500" />
                                        <div>
                                            <p className="text-sm text-gray-500">{t('Owner')}</p>
                                            <p className="font-medium">{company.owner?.name}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-start">
                                        {company.verification_status === 'verified' ? (
                                            <CheckCircle size={18} className="mt-1 mr-2 text-green-500" />
                                        ) : (
                                            <XCircle size={18} className="mt-1 mr-2 text-red-500" />
                                        )}
                                        <div>
                                            <p className="text-sm text-gray-500">Verification Status</p>
                                            <p
                                                className={`font-medium ${company.verification_status === 'verified' ? 'text-green-600' : 'text-red-600'}`}
                                            >
                                                {company.verification_status.charAt(0).toUpperCase() + company.verification_status.slice(1)}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-start">
                                        <Calendar size={18} className="mt-1 mr-2 text-gray-500" />
                                        <div>
                                            <p className="text-sm text-gray-500">Created On</p>
                                            <p className="font-medium">
                                                {new Date(company.created_at).toLocaleDateString('en-US', {
                                                    year: 'numeric',
                                                    month: 'long',
                                                    day: 'numeric',
                                                })}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* You can add more sections here like company employees, transactions, etc. */}
                        <div className="rounded-lg bg-gray-50 p-4">
                            <h2 className="mb-4 text-lg font-medium">Company Users</h2>
                            {/* If you have company users data, you can map through them here */}
                            <p className="text-gray-500">No users found for this company.</p>
                            {/* Replace with actual user data if available */}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
