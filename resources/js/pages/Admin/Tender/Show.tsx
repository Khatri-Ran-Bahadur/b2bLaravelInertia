import AppLayout from '@/layouts/app-layout';
import { Head, router } from '@inertiajs/react';
import { format } from 'date-fns';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { ArrowLeft, Building, Calendar, DollarSign, Mail, MapPin, Phone, Tag } from 'lucide-react';

interface Tender {
    id: number;
    title: string;
    description: string;
    status: string;
    budget_from: number;
    budget_to: number;
    phone: string;
    email: string;
    location: string;
    created_at: string;
    company: {
        id: number;
        name: string;
        email: string;
        phone: string;
        tin_number: string;
        owner: {
            id: number;
            first_name: string;
            last_name: string;
            email: string;
            phone: string;
            image: string;
        };
    };
    tender_category: {
        id: number;
        name: string;
        slug: string;
    };
    tender_products: Array<{
        id: number;
        product_name: string;
        quantity: number;
        unit: string;
    }>;
    media: Array<{
        id: number;
        size: number;
        url: string;
    }>;
}

interface Props {
    tender: Tender;
}

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

export default function Show({ tender }: Props) {
    const { t } = useLaravelReactI18n();

    return (
        <AppLayout>
            <Head title={tender.title} />

            <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
                {/* Header */}
                <div className="bg-white shadow dark:bg-gray-800">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <button
                                    onClick={() => router.visit(route('admin.tenders.index'))}
                                    className="mr-4 rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                                >
                                    <ArrowLeft className="h-5 w-5 text-gray-500 dark:text-gray-400" />
                                </button>
                                <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{tender.title}</h1>
                            </div>
                            <div className="flex items-center space-x-4">
                                <span
                                    className={`rounded-full px-3 py-1 text-sm font-medium ${
                                        tender.status === 'open'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    }`}
                                >
                                    {t(tender.status)}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Main Content */}
                <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                        {/* Left Column */}
                        <div className="space-y-8 lg:col-span-2">
                            {/* Description */}
                            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Description')}</h2>
                                <p className="whitespace-pre-line text-gray-600 dark:text-gray-300">{tender.description}</p>
                            </div>

                            {/* Products */}
                            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Products')}</h2>
                                <div className="space-y-4">
                                    {tender.tender_products.map((product) => (
                                        <div key={product.id} className="border-b border-gray-200 pb-4 last:border-0 dark:border-gray-700">
                                            <div className="flex items-start justify-between">
                                                <div>
                                                    <h3 className="font-medium text-gray-900 dark:text-white">{product.product_name}</h3>
                                                </div>
                                                <div className="text-right">
                                                    <p className="font-medium text-gray-900 dark:text-white">
                                                        {product.quantity} {product.unit}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Images */}
                            {tender.media.length > 0 && (
                                <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                    <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Images')}</h2>
                                    <div className="grid grid-cols-2 gap-4 md:grid-cols-3">
                                        {tender.media.map((image) => (
                                            <div key={image.id} className="aspect-w-16 aspect-h-9">
                                                <img src={image.url} alt={tender.title} className="rounded-lg object-cover" />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Right Column */}
                        <div className="space-y-8">
                            {/* Company Info */}
                            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Company')}</h2>
                                <div className="space-y-4">
                                    <div className="flex items-center space-x-4">
                                        {tender.company.owner?.image && (
                                            <img
                                                src={tender.company.owner.image}
                                                alt={tender.company.owner.first_name}
                                                className="h-12 w-12 rounded-full object-cover"
                                            />
                                        )}
                                        <div>
                                            <h3 className="font-medium text-gray-900 dark:text-white">{tender.company.name}</h3>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                                {tender.company.owner?.first_name} {tender.company.owner?.last_name}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="space-y-2">
                                        <div className="flex items-center space-x-3">
                                            <Mail className="h-5 w-5 text-gray-400" />
                                            <p className="text-gray-900 dark:text-white">{tender.company.email}</p>
                                        </div>
                                        <div className="flex items-center space-x-3">
                                            <Phone className="h-5 w-5 text-gray-400" />
                                            <p className="text-gray-900 dark:text-white">{tender.company.phone}</p>
                                        </div>
                                        <div className="flex items-center space-x-3">
                                            <Building className="h-5 w-5 text-gray-400" />
                                            <p className="text-gray-900 dark:text-white">{tender.company.tin_number}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Details */}
                            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Details')}</h2>
                                <div className="space-y-4">
                                    <div className="flex items-center space-x-3">
                                        <DollarSign className="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{t('Budget')}</p>
                                            <p className="font-medium text-gray-900 dark:text-white">
                                                {tender.budget_from} - {tender.budget_to}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-3">
                                        <MapPin className="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{t('Location')}</p>
                                            <p className="font-medium text-gray-900 dark:text-white">{tender.location}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-3">
                                        <Tag className="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{t('Category')}</p>
                                            <p className="font-medium text-gray-900 dark:text-white">{tender.tender_category.name}</p>
                                        </div>
                                    </div>
                                    <div className="flex items-center space-x-3">
                                        <Calendar className="h-5 w-5 text-gray-400" />
                                        <div>
                                            <p className="text-sm text-gray-500 dark:text-gray-400">{t('Created At')}</p>
                                            <p className="font-medium text-gray-900 dark:text-white">{format(new Date(tender.created_at), 'PPP')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Contact Info */}
                            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                                <h2 className="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{t('Contact Information')}</h2>
                                <div className="space-y-4">
                                    <div className="flex items-center space-x-3">
                                        <Phone className="h-5 w-5 text-gray-400" />
                                        <p className="text-gray-900 dark:text-white">{tender.phone}</p>
                                    </div>
                                    <div className="flex items-center space-x-3">
                                        <Mail className="h-5 w-5 text-gray-400" />
                                        <p className="text-gray-900 dark:text-white">{tender.email}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
