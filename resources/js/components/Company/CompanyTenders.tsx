import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Company } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import axios from 'axios';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Calendar, DollarSign, Grid2X2, List, Phone } from 'lucide-react';
import { useState } from 'react';

interface Tender {
    id: number;
    title: string;
    description: string;
    budget: number;
    phone: string;
    location: string;
    status: string;
    created_at: string;
    images: string;
}

interface Meta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface CompanyTendersProps {
    company: Company;
    tenders: Tender[];
    meta?: Meta;
}

export default function CompanyTenders({ company }: CompanyTendersProps) {
    const { t } = useLaravelReactI18n();
    const { initialTenders, initialMeta } = usePage().props;

    const [tenders, setTenders] = useState<Tender[]>(initialTenders as Tender[]);
    const [meta, setMeta] = useState<Meta>(initialMeta as Meta);
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [isLoadingMore, setIsLoadingMore] = useState(false);

    const hasMore = meta.current_page < meta.last_page;

    const handleLoadMore = async () => {
        if (isLoadingMore) return;

        setIsLoadingMore(true);
        try {
            const response = await axios.get(route('admin.companies.tenders', company.id), {
                params: {
                    page: meta.current_page + 1,
                    per_page: meta.per_page,
                },
            });

            const { tenders: newTenders, meta: newMeta } = response.data;
            setTenders([...tenders, ...newTenders]);
            setMeta(newMeta);
        } catch (error) {
            console.error('Error loading more tenders:', error);
        } finally {
            setIsLoadingMore(false);
        }
    };

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                    {t('Tenders')} ({meta.total})
                </h2>
                <div className="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setViewMode('grid')}
                        className={cn(viewMode === 'grid' && 'bg-gray-100 dark:bg-gray-800')}
                    >
                        <Grid2X2 className="h-4 w-4" />
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => setViewMode('list')}
                        className={cn(viewMode === 'list' && 'bg-gray-100 dark:bg-gray-800')}
                    >
                        <List className="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div className={cn('grid gap-4', viewMode === 'grid' ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' : 'grid-cols-1')}>
                {tenders.map((tender) => (
                    <div
                        key={tender.id}
                        className={cn(
                            'group relative overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg dark:border-gray-700 dark:bg-gray-800',
                            viewMode === 'list' && 'flex gap-4',
                        )}
                    >
                        <div
                            className={cn(
                                'relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700',
                                viewMode === 'list' && 'h-40 w-40 flex-shrink-0',
                            )}
                        >
                            {tender.images ? (
                                <img
                                    src={tender.images}
                                    alt={tender.title}
                                    className="h-full w-full object-cover transition-transform group-hover:scale-105"
                                />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center">
                                    <span className="text-2xl text-gray-400">📄</span>
                                </div>
                            )}
                        </div>

                        <div className="flex flex-1 flex-col p-4">
                            <div className="mb-2 flex items-start justify-between">
                                <div>
                                    <h3 className="font-medium text-gray-900 dark:text-white">{tender.title}</h3>
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{tender.description}</p>
                                </div>
                                <span
                                    className={cn(
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        tender.status === 'active'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    )}
                                >
                                    {tender.status}
                                </span>
                            </div>

                            <div className="mt-2 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                                <div className="flex items-center">
                                    <DollarSign className="mr-2 h-4 w-4" />
                                    {new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD',
                                    }).format(tender.budget)}
                                </div>
                                <div className="flex items-center">
                                    <Calendar className="mr-2 h-4 w-4" />
                                    {new Date(tender.created_at).toLocaleDateString()}
                                </div>
                                <div className="flex items-center">
                                    <Phone className="mr-2 h-4 w-4" />
                                    {tender.phone}
                                </div>
                            </div>

                            <div className="mt-4 flex items-center justify-end">
                                <Link
                                    href={route('admin.tenders.show', tender.id)}
                                    className="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    {t('View Details')}
                                </Link>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {hasMore && (
                <div className="mt-6 flex justify-center">
                    <Button variant="outline" size="lg" onClick={handleLoadMore} disabled={isLoadingMore}>
                        {isLoadingMore ? t('Loading...') : t('Load More')}
                    </Button>
                </div>
            )}
        </div>
    );
}
