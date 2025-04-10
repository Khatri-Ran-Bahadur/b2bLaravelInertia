import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { AlertCircle, ArrowLeft, Save, TagIcon, X } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Tender Categories', href: route('admin.tender-categories.index') },
    { title: 'Update Category', href: '' },
];

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('admin.tender-categories.store'));
    };

    const { t } = useLaravelReactI18n();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Update Tender Category" />

            <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 sm:p-6 lg:p-8 dark:from-gray-900 dark:to-gray-800">
                <Card className="overflow-hidden border-none bg-white shadow-xl dark:bg-gray-800">
                    <CardHeader className="">
                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div className="flex items-center gap-4">
                                <div className="bg-primary/20 dark:bg-primary/30 rounded-xl p-3 shadow-sm backdrop-blur-sm">
                                    <TagIcon className="text-primary dark:text-primary-light" size={24} />
                                </div>
                                <div>
                                    <h1 className="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{t('Create Tender Category')}</h1>
                                    <p className="mt-1 text-sm text-gray-500 dark:text-gray-300">
                                        {t('Edit the details for category')}{' '}
                                        <span className="text-primary dark:text-primary-light font-medium"></span>
                                    </p>
                                </div>
                            </div>

                            <Link href={route('admin.tender-categories.index')}>
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    className="hover:text-primary dark:hover:text-primary-light flex items-center gap-2 text-gray-700 transition-all hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <ArrowLeft size={16} />
                                    {t('Back')}
                                </Button>
                            </Link>
                        </div>
                    </CardHeader>

                    <CardContent className="p-0">
                        <form onSubmit={handleSubmit} className="p-6">
                            <div className="mx-auto max-w-xl space-y-6">
                                <div className="space-y-2">
                                    <Label htmlFor="name" className="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                                        <TagIcon size={14} className="text-primary dark:text-primary-light" />
                                        {t('Category Name')}
                                    </Label>

                                    <div className="group relative">
                                        <Input
                                            id="name"
                                            name="name"
                                            value={data.name}
                                            onChange={(e) => setData('name', e.target.value)}
                                            className="focus:border-primary focus:ring-primary/20 dark:focus:border-primary-light dark:focus:ring-primary-light/20 h-12 w-full rounded-lg border border-gray-200 bg-white/80 pl-10 text-base text-gray-900 shadow-sm backdrop-blur-sm transition-all group-hover:border-gray-300 focus:ring-2 dark:border-gray-600 dark:bg-gray-800/80 dark:text-gray-100 dark:group-hover:border-gray-500"
                                            placeholder={t('Enter category name')}
                                            required
                                            autoFocus
                                        />
                                        <TagIcon
                                            size={18}
                                            className="group-hover:text-primary dark:group-hover:text-primary-light absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 transition-colors dark:text-gray-500"
                                        />
                                    </div>

                                    {errors.name && (
                                        <div className="mt-2 flex items-center gap-2 rounded-md bg-red-50 p-2 text-sm text-red-500 dark:bg-red-900/20 dark:text-red-400">
                                            <AlertCircle size={14} className="flex-shrink-0" />
                                            <span>{errors.name}</span>
                                        </div>
                                    )}

                                    <p className="mt-2 ml-1 text-xs text-gray-500 dark:text-gray-400">
                                        {t('Choose a descriptive name that clearly identifies the tender category')}
                                    </p>
                                </div>

                                <div className="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-6 dark:border-gray-700">
                                    <Link href={route('admin.tender-categories.index')}>
                                        <Button
                                            variant="outline"
                                            type="button"
                                            className="bg-gray border-gray-900 text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white"
                                        >
                                            <X size={16} className="mr-2" />
                                            <span>{t('Cancel')}</span>
                                        </Button>
                                    </Link>

                                    <Button
                                        variant="outline"
                                        type="submit"
                                        disabled={processing}
                                        className="bg-gray border-gray-900 text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 dark:hover:text-white"
                                    >
                                        <Save size={16} className="mr-2" />
                                        <span>{processing ? t('Saving...') : t('Save Changes')}</span>
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
