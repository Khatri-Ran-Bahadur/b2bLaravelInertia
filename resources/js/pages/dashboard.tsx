import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { AlertCircle, BarChart2, Building, Clock, FileText, Globe, TrendingUp, Users } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard() {
    // Sample data - replace with actual data fetching logic
    const stats = {
        totalCompanies: 247,
        totalTenders: 128,
        totalUsers: 1896,
        activeProjects: 34,
        conversionRate: 76.2,
        pendingRequests: 17,
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                {/* Stats Cards */}
                <div className="grid gap-6 md:grid-cols-3 lg:grid-cols-4">
                    {/* Companies Card */}
                    <div className="flex flex-col rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div className="flex items-center justify-between">
                            <h3 className="font-medium text-gray-500 dark:text-gray-400">Total Companies</h3>
                            <div className="rounded-full bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                <Building size={20} />
                            </div>
                        </div>
                        <div className="mt-4 flex items-baseline">
                            <span className="text-3xl font-bold text-gray-900 dark:text-white">{stats.totalCompanies}</span>
                            <span className="ml-2 text-sm font-medium text-green-500">
                                +12% <span className="text-gray-400">this month</span>
                            </span>
                        </div>
                    </div>

                    {/* Tenders Card */}
                    <div className="flex flex-col rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div className="flex items-center justify-between">
                            <h3 className="font-medium text-gray-500 dark:text-gray-400">Total Tenders</h3>
                            <div className="rounded-full bg-purple-100 p-2 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                <FileText size={20} />
                            </div>
                        </div>
                        <div className="mt-4 flex items-baseline">
                            <span className="text-3xl font-bold text-gray-900 dark:text-white">{stats.totalTenders}</span>
                            <span className="ml-2 text-sm font-medium text-green-500">
                                +5% <span className="text-gray-400">this month</span>
                            </span>
                        </div>
                    </div>

                    {/* Users Card */}
                    <div className="flex flex-col rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div className="flex items-center justify-between">
                            <h3 className="font-medium text-gray-500 dark:text-gray-400">Total Users</h3>
                            <div className="rounded-full bg-green-100 p-2 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                <Users size={20} />
                            </div>
                        </div>
                        <div className="mt-4 flex items-baseline">
                            <span className="text-3xl font-bold text-gray-900 dark:text-white">{stats.totalUsers}</span>
                            <span className="ml-2 text-sm font-medium text-green-500">
                                +23% <span className="text-gray-400">this month</span>
                            </span>
                        </div>
                    </div>

                    {/* Conversion Rate Card */}
                    <div className="flex flex-col rounded-xl border bg-white p-6 shadow-sm transition-all hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        <div className="flex items-center justify-between">
                            <h3 className="font-medium text-gray-500 dark:text-gray-400">Conversion Rate</h3>
                            <div className="rounded-full bg-amber-100 p-2 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                <TrendingUp size={20} />
                            </div>
                        </div>
                        <div className="mt-4 flex items-baseline">
                            <span className="text-3xl font-bold text-gray-900 dark:text-white">{stats.conversionRate}%</span>
                            <span className="ml-2 text-sm font-medium text-green-500">
                                +3.2% <span className="text-gray-400">this month</span>
                            </span>
                        </div>
                    </div>
                </div>

                {/* Charts and Activity Section */}
                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Tender Activity Chart (2 columns wide) */}
                    <div className="rounded-xl border bg-white p-6 shadow-sm lg:col-span-2 dark:border-gray-700 dark:bg-gray-800">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white">Tender Activity</h3>
                            <div className="flex items-center space-x-2">
                                <button className="rounded-md bg-blue-50 px-3 py-1 text-sm font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    Monthly
                                </button>
                                <button className="rounded-md px-3 py-1 text-sm font-medium text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700">
                                    Weekly
                                </button>
                            </div>
                        </div>
                        <div className="relative h-72 w-full">
                            <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/10 dark:stroke-neutral-100/10" />
                            <div className="absolute inset-0 flex items-center justify-center">
                                <div className="flex items-center text-gray-500 dark:text-gray-400">
                                    <BarChart2 className="mr-2" size={20} />
                                    <span>Tender activity chart will display here</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Recent Activity */}
                    <div className="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 className="mb-4 text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                        <div className="space-y-4">
                            <div className="flex items-start">
                                <div className="mr-3 rounded-full bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    <FileText size={16} />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">New tender submitted</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">Tech Infrastructure Project • 2h ago</p>
                                </div>
                            </div>
                            <div className="flex items-start">
                                <div className="mr-3 rounded-full bg-green-100 p-2 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                    <Users size={16} />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">New company registered</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">Acme Corporation • 5h ago</p>
                                </div>
                            </div>
                            <div className="flex items-start">
                                <div className="mr-3 rounded-full bg-purple-100 p-2 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                    <Globe size={16} />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">International tender closed</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">Global Supply Chain • 1d ago</p>
                                </div>
                            </div>
                            <div className="flex items-start">
                                <div className="mr-3 rounded-full bg-amber-100 p-2 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                    <Clock size={16} />
                                </div>
                                <div>
                                    <p className="text-sm font-medium text-gray-900 dark:text-white">Tender deadline extended</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400">City Development Project • 1d ago</p>
                                </div>
                            </div>
                        </div>
                        <button className="mt-4 w-full rounded-lg border border-gray-200 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                            View All Activity
                        </button>
                    </div>
                </div>

                {/* Bottom Section */}
                <div className="grid gap-6 md:grid-cols-2">
                    {/* Upcoming Deadlines */}
                    <div className="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <h3 className="mb-4 text-lg font-medium text-gray-900 dark:text-white">Upcoming Deadlines</h3>
                        <div className="space-y-3">
                            <div className="flex items-center justify-between rounded-lg border border-gray-100 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                                <div className="flex items-center">
                                    <div className="h-10 w-10 flex-shrink-0 rounded-lg bg-red-100 p-2 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                        <AlertCircle size={24} />
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-gray-900 dark:text-white">Healthcare Equipment Tender</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Ministry of Health</p>
                                    </div>
                                </div>
                                <span className="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-600 dark:bg-red-900/30 dark:text-red-400">
                                    Today
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700">
                                <div className="flex items-center">
                                    <div className="h-10 w-10 flex-shrink-0 rounded-lg bg-amber-100 p-2 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                        <Clock size={24} />
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-gray-900 dark:text-white">IT Infrastructure Project</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Tech Solutions Inc.</p>
                                    </div>
                                </div>
                                <span className="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                    2 days
                                </span>
                            </div>
                            <div className="flex items-center justify-between rounded-lg border border-gray-100 p-3 dark:border-gray-700">
                                <div className="flex items-center">
                                    <div className="h-10 w-10 flex-shrink-0 rounded-lg bg-blue-100 p-2 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        <FileText size={24} />
                                    </div>
                                    <div className="ml-3">
                                        <p className="font-medium text-gray-900 dark:text-white">Construction Services</p>
                                        <p className="text-xs text-gray-500 dark:text-gray-400">Urban Development Corp</p>
                                    </div>
                                </div>
                                <span className="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    5 days
                                </span>
                            </div>
                        </div>
                    </div>

                    {/* Active Projects */}
                    <div className="rounded-xl border bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-white">Active Projects</h3>
                            <span className="rounded-full bg-blue-100 px-2.5 py-0.5 text-sm font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                {stats.activeProjects} projects
                            </span>
                        </div>
                        <div className="space-y-4">
                            <div>
                                <div className="mb-1 flex items-center justify-between">
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Government Infrastructure</span>
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">75%</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div className="h-2 rounded-full bg-blue-600 dark:bg-blue-500" style={{ width: '75%' }}></div>
                                </div>
                            </div>
                            <div>
                                <div className="mb-1 flex items-center justify-between">
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Healthcare Supplies</span>
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">45%</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div className="h-2 rounded-full bg-green-600 dark:bg-green-500" style={{ width: '45%' }}></div>
                                </div>
                            </div>
                            <div>
                                <div className="mb-1 flex items-center justify-between">
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Technology Services</span>
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">90%</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div className="h-2 rounded-full bg-purple-600 dark:bg-purple-500" style={{ width: '90%' }}></div>
                                </div>
                            </div>
                            <div>
                                <div className="mb-1 flex items-center justify-between">
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Construction Projects</span>
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">30%</span>
                                </div>
                                <div className="h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
                                    <div className="h-2 rounded-full bg-amber-600 dark:bg-amber-500" style={{ width: '30%' }}></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
