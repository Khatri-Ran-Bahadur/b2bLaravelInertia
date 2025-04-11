import { Button } from '@/components/ui/button';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { cn } from '@/lib/utils';
import { Company } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { useLaravelReactI18n } from 'laravel-react-i18n';
import { Calendar, Download, Eye, FileText, Grid2X2, List } from 'lucide-react';
import { useState } from 'react';

interface Document {
    id: number;
    name: string;
    company_id: number;
    size: number;
    type: string;
    created_at: string;
    file_path: string;
    images: string[];
}

interface CompanyDocumentsProps {
    company: Company;
}

export default function CompanyDocuments({ company }: CompanyDocumentsProps) {
    const { t } = useLaravelReactI18n();
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [selectedDocument, setSelectedDocument] = useState<Document | null>(null);
    const [isPreviewOpen, setIsPreviewOpen] = useState(false);

    const { documents } = usePage().props;

    const formatFileSize = (bytes: number) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const getFileIcon = (fileType: string) => {
        console.log(fileType);
        const iconMap: { [key: string]: string } = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            jpg: '🖼️',
            jpeg: '🖼️',
            png: '🖼️',
            gif: '🖼️',
        };
        const extension = fileType.toLowerCase().replace('.', '');
        return iconMap[extension] || '📁';
    };

    const renderFilePreview = (document: Document) => {
        const fileType = document.type.toLowerCase();

        if (fileType.includes('pdf')) {
            return <iframe src={document.file_path} className="h-full w-full" title={document.name} />;
        } else if (fileType.includes('image')) {
            return (
                <div className="flex gap-2 overflow-x-auto">
                    {document.images?.map((image, index) => (
                        <img key={index} src={image} alt={`${document.name} ${index + 1}`} className="h-full w-full object-contain" />
                    ))}
                </div>
            );
        } else {
            return (
                <div className="flex h-full w-full items-center justify-center">
                    <span className="text-4xl">{getFileIcon(document.type)}</span>
                </div>
            );
        }
    };

    const handleView = (document: Document) => {
        setSelectedDocument(document);
        setIsPreviewOpen(true);
    };

    return (
        <div>
            <div className="mb-6 flex items-center justify-between">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                    {t('Documents')} ({documents.length})
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
                {documents.map((document: Document) => (
                    <div
                        key={document.id}
                        className={cn(
                            'group relative overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg dark:border-gray-700 dark:bg-gray-800',
                            viewMode === 'list' && 'flex items-center gap-4',
                        )}
                    >
                        <div
                            className={cn(
                                'relative flex aspect-square items-center justify-center bg-gray-100 dark:bg-gray-700',
                                viewMode === 'list' && 'h-20 w-20 flex-shrink-0',
                            )}
                        >
                            {renderFilePreview(document)}
                        </div>

                        <div className="flex flex-1 flex-col p-4">
                            <div className="mb-2">
                                <h3 className="font-medium text-gray-900 dark:text-white">{document.name}</h3>
                                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">{formatFileSize(document.size)}</p>
                            </div>

                            <div className="mt-2 space-y-2 text-sm text-gray-500 dark:text-gray-400">
                                <div className="flex items-center">
                                    <FileText className="mr-2 h-4 w-4" />
                                    {document.type.toUpperCase()}
                                </div>
                                <div className="flex items-center">
                                    <Calendar className="mr-2 h-4 w-4" />
                                    {new Date(document.created_at).toLocaleDateString()}
                                </div>
                            </div>

                            <div className="mt-4 flex items-center justify-end gap-2">
                                <button
                                    onClick={() => handleView(document)}
                                    className="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <Eye className="mr-1 h-4 w-4" />
                                    {t('View')}
                                </button>
                                <Link
                                    href={`${document.file_path}?download`}
                                    className="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    <Download className="mr-1 h-4 w-4" />
                                    {t('Download')}
                                </Link>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            <Dialog open={isPreviewOpen} onOpenChange={setIsPreviewOpen}>
                <DialogContent className="h-[90vh] w-full">
                    <div className="h-full w-full">{selectedDocument && renderFilePreview(selectedDocument)}</div>
                </DialogContent>
            </Dialog>
        </div>
    );
}
