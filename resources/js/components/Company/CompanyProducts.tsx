import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Company } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import axios from 'axios';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Grid2X2, List } from 'lucide-react';
import { useState } from 'react';

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    status: string;
    created_at: string;
    avg_rating: number;
    images: string;
}

interface Meta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface CompanyProductsProps {
    company: Company;
    initialProducts: Product[];
    meta?: Meta;
}

export default function CompanyProducts({ company }: CompanyProductsProps) {
    const { initialProducts, initialMeta } = usePage().props;
    const { t } = useLaravelReactI18n();
    const [products, setProducts] = useState<Product[]>(initialProducts as Product[]);

    const [meta, setMeta] = useState<Meta>(initialMeta as Meta);
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [isLoadingMore, setIsLoadingMore] = useState(false);

    const hasMore = meta?.current_page < meta?.last_page;

    const handleLoadMore = async () => {
        if (isLoadingMore) return;

        setIsLoadingMore(true);
        try {
            const response = await axios.get(route('admin.companies.products', company.id), {
                params: {
                    page: meta.current_page + 1,
                    per_page: meta.per_page,
                },
            });

            const { products: newProducts, meta: newMeta } = response.data;
            setProducts([...products, ...newProducts]);
            setMeta(newMeta);
        } catch (error) {
            console.error('Error loading more products:', error);
        } finally {
            setIsLoadingMore(false);
        }
    };

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                    {t('Products')} ({meta.total})
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
                {products.map((product) => (
                    <div
                        key={product.id}
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
                            {product.images?.[0] ? (
                                <img
                                    src={product.images}
                                    alt={product.name}
                                    className="h-full w-full object-cover transition-transform group-hover:scale-105"
                                />
                            ) : (
                                <div className="flex h-full w-full items-center justify-center">
                                    <span className="text-2xl text-gray-400">📷</span>
                                </div>
                            )}
                        </div>

                        <div className="flex flex-1 flex-col p-4">
                            <div className="mb-2 flex items-start justify-between">
                                <div>
                                    <h3 className="font-medium text-gray-900 dark:text-white">{product.name}</h3>
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {product.description.split(' ').slice(0, 3).join(' ')}...
                                    </p>
                                </div>
                                <span
                                    className={cn(
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                        product.status === 'active'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    )}
                                >
                                    {product.status}
                                </span>
                            </div>

                            <div className="mt-auto flex items-center justify-between">
                                <div className="font-medium text-gray-900 dark:text-white">
                                    {new Intl.NumberFormat('en-US', {
                                        style: 'currency',
                                        currency: 'USD',
                                    }).format(product.price)}
                                </div>
                                <Link href="#" className="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
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
