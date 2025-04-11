import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { Company } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import axios from 'axios';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { CheckCircle, MessageCircle, Star, ThumbsUp } from 'lucide-react';
import { useState } from 'react';

interface User {
    id: number;
    name: string;
    first_name: string;
    last_name: string;
    image?: string;
}

interface Reply {
    id: number;
    content: string;
    created_at: string;
    user: User;
}

interface Product {
    id: number;
    name: string;
    image?: string;
}

interface Review {
    id: number;
    rating: number;
    content: string;
    created_at: string;
    user: User;
    product: Product;
    images: string[];
    replies?: Reply[]; // make optional
    likes_count: number;
    is_verified_purchase: boolean;
}

interface Meta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    average_rating: number;
    rating_counts: { [key: number]: number };
}

interface CompanyReviewsProps {
    company: Company;
}

export default function CompanyReviews({ company }: CompanyReviewsProps) {
    const { initialReviews, initialMeta } = usePage().props;
    const { t } = useLaravelReactI18n();

    const [reviews, setReviews] = useState<Review[]>(initialReviews || []);
    const [meta, setMeta] = useState<Meta>(initialMeta);
    const [isLoadingMore, setIsLoadingMore] = useState(false);
    const [expandedReplies, setExpandedReplies] = useState<number[]>([]);

    const hasMore = meta.current_page < meta.last_page;

    const handleLoadMore = async () => {
        if (isLoadingMore) return;

        setIsLoadingMore(true);
        try {
            const response = await axios.get(route('admin.companies.reviews', company.id), {
                params: {
                    page: meta.current_page + 1,
                    per_page: meta.per_page,
                },
            });

            const { reviews: newReviews, meta: newMeta } = response.data;
            setReviews([...reviews, ...newReviews]);
            setMeta(newMeta);
        } catch (error) {
            console.error('Error loading more reviews:', error);
        } finally {
            setIsLoadingMore(false);
        }
    };

    const toggleReplies = (reviewId: number) => {
        setExpandedReplies((prev) => (prev.includes(reviewId) ? prev.filter((id) => id !== reviewId) : [...prev, reviewId]));
    };

    const renderStars = (rating: number) => {
        return Array.from({ length: 5 }).map((_, index) => (
            <Star key={index} className={cn('h-4 w-4', index < rating ? 'fill-yellow-400 text-yellow-400' : 'fill-gray-200 text-gray-200')} />
        ));
    };

    const calculateRatingPercentage = (rating: number) => {
        const count = meta.rating_counts[rating] || 0;
        return (count / meta.total) * 100;
    };

    return (
        <div className="mb-8">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                {t('Product Reviews')} ({meta.total})
            </h2>

            <div className="mt-4 grid gap-8 md:grid-cols-12">
                {/* Summary */}
                <div className="md:col-span-4">
                    <div className="rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <div className="mb-4 text-center">
                            <div className="text-4xl font-bold text-gray-900 dark:text-white">{meta.average_rating.toFixed(1)}</div>
                            <div className="mt-2 flex justify-center">{renderStars(Math.round(meta.average_rating))}</div>
                            <div className="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {meta.total} {t('reviews')}
                            </div>
                        </div>

                        <div className="space-y-2">
                            {[5, 4, 3, 2, 1].map((rating) => (
                                <div key={rating} className="flex items-center gap-2">
                                    <div className="flex w-24 items-center text-sm">{renderStars(rating)}</div>
                                    <div className="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div
                                            className="h-full rounded-full bg-yellow-400"
                                            style={{ width: `${calculateRatingPercentage(rating)}%` }}
                                        />
                                    </div>
                                    <div className="w-12 text-right text-sm text-gray-500">{calculateRatingPercentage(rating).toFixed(0)}%</div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Reviews */}
                <div className="space-y-6 md:col-span-8">
                    {reviews.map((review) => {
                        const replies = review.replies || [];

                        return (
                            <div key={review.id} className="rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                                <div className="mb-4">
                                    <Link
                                        href="#"
                                        className="mb-3 flex items-center gap-3 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
                                    >
                                        {review.product.image ? (
                                            <img src={review.product.image} alt={review.product.name} className="h-12 w-12 rounded-lg object-cover" />
                                        ) : (
                                            <div className="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
                                                <span className="text-2xl text-gray-400">📦</span>
                                            </div>
                                        )}
                                        <span className="font-medium">{review.product.name}</span>
                                    </Link>

                                    <div className="flex items-start justify-between">
                                        <div className="flex items-start gap-3">
                                            {review.user.image ? (
                                                <img src={review.user.image} alt={review.user.name} className="h-10 w-10 rounded-full object-cover" />
                                            ) : (
                                                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                                    <span className="text-lg font-medium text-gray-600 dark:text-gray-300">
                                                        {review.user.name[0]}
                                                    </span>
                                                </div>
                                            )}
                                            <div>
                                                <div className="flex items-center gap-2">
                                                    <span className="font-medium text-gray-900 dark:text-white">{review.user.name}</span>
                                                    {review.is_verified_purchase && <CheckCircle className="h-4 w-4 text-green-500" />}
                                                </div>
                                                <div className="mt-1 flex items-center gap-2">
                                                    <div className="flex">{renderStars(review.rating)}</div>
                                                    <span className="text-sm text-gray-500">{new Date(review.created_at).toLocaleDateString()}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p className="text-gray-700 dark:text-gray-300">{review.content}</p>

                                {review.images.length > 0 && (
                                    <div className="mt-4 flex gap-2 overflow-x-auto">
                                        {review.images.map((image, index) => (
                                            <img key={index} src={image} alt="" className="h-20 w-20 rounded-lg object-cover" />
                                        ))}
                                    </div>
                                )}

                                <div className="mt-4 flex items-center gap-4">
                                    <button className="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700">
                                        <ThumbsUp className="h-4 w-4" />
                                        {review.likes_count}
                                    </button>
                                    <button
                                        onClick={() => toggleReplies(review.id)}
                                        className="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700"
                                    >
                                        <MessageCircle className="h-4 w-4" />
                                        {replies.length} {t('replies')}
                                    </button>
                                </div>

                                {expandedReplies.includes(review.id) && replies.length > 0 && (
                                    <div className="mt-4 space-y-4 border-t pt-4 dark:border-gray-700">
                                        {replies.map((reply) => (
                                            <div key={reply.id} className="flex gap-3">
                                                {reply.user.image ? (
                                                    <img src={reply.user.image} alt={reply.user.name} className="h-8 w-8 rounded-full object-cover" />
                                                ) : (
                                                    <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                                                        <span className="text-sm font-medium text-gray-600 dark:text-gray-300">
                                                            {reply.user.first_name[0]}
                                                        </span>
                                                    </div>
                                                )}
                                                <div>
                                                    <div className="flex items-center gap-2">
                                                        <span className="font-medium text-gray-900 dark:text-white">{reply.user.name}</span>
                                                        <span className="text-sm text-gray-500">
                                                            {new Date(reply.created_at).toLocaleDateString()}
                                                        </span>
                                                    </div>
                                                    <p className="mt-1 text-gray-700 dark:text-gray-300">{reply.content}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        );
                    })}

                    {hasMore && (
                        <div className="mt-6 flex justify-center">
                            <Button variant="outline" size="lg" onClick={handleLoadMore} disabled={isLoadingMore}>
                                {isLoadingMore ? t('Loading...') : t('Load More Reviews')}
                            </Button>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
