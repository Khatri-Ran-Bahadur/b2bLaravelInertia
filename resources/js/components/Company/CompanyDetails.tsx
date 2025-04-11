import { Company } from '@/types';
import { Building2, Calendar, CheckCircle, FileText, Phone, User, XCircle } from 'lucide-react';

interface CompanyDetailsProps {
    company: Company;
}

export default function CompanyDetails({ company }: CompanyDetailsProps) {
    return (
        <div className="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div className="rounded-lg bg-gray-50 p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-gray-700">
                <h2 className="mb-4 border-b border-gray-200 pb-2 text-lg font-medium text-gray-900 dark:border-gray-600 dark:text-white">
                    Basic Information
                </h2>
                <div className="space-y-3">
                    <div className="flex items-start">
                        <Building2 size={18} className="mt-1 mr-2 text-blue-500 dark:text-blue-400" />
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Company Name</p>
                            <p className="font-medium text-gray-900 dark:text-white">{company.name}</p>
                        </div>
                    </div>
                    <div className="flex items-start">
                        <FileText size={18} className="mt-1 mr-2 text-blue-500 dark:text-blue-400" />
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">TIN Number</p>
                            <p className="font-medium text-gray-900 dark:text-white">{company.tin_number}</p>
                        </div>
                    </div>
                    <div className="flex items-start">
                        <Phone size={18} className="mt-1 mr-2 text-blue-500 dark:text-blue-400" />
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Phone Number</p>
                            <p className="font-medium text-gray-900 dark:text-white">{company.phone}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div className="rounded-lg bg-gray-50 p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:bg-gray-700">
                <h2 className="mb-4 border-b border-gray-200 pb-2 text-lg font-medium text-gray-900 dark:border-gray-600 dark:text-white">
                    Additional Details
                </h2>
                <div className="space-y-3">
                    <div className="flex items-start">
                        <User size={18} className="mt-1 mr-2 text-blue-500 dark:text-blue-400" />
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Owner</p>
                            <p className="font-medium text-gray-900 dark:text-white">{company.owner?.name}</p>
                        </div>
                    </div>
                    <div className="flex items-start">
                        {company.verification_status === 'verified' ? (
                            <CheckCircle size={18} className="mt-1 mr-2 text-green-500 dark:text-green-400" />
                        ) : (
                            <XCircle size={18} className="mt-1 mr-2 text-red-500 dark:text-red-400" />
                        )}
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Verification Status</p>
                            <p
                                className={`font-medium ${
                                    company.verification_status === 'verified'
                                        ? 'text-green-600 dark:text-green-400'
                                        : 'text-red-600 dark:text-red-400'
                                }`}
                            >
                                {company.verification_status.charAt(0).toUpperCase() + company.verification_status.slice(1)}
                            </p>
                        </div>
                    </div>
                    <div className="flex items-start">
                        <Calendar size={18} className="mt-1 mr-2 text-blue-500 dark:text-blue-400" />
                        <div>
                            <p className="text-sm text-gray-500 dark:text-gray-400">Created On</p>
                            <p className="font-medium text-gray-900 dark:text-white">
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
    );
}
