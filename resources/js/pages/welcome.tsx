import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Welcome" />

            <div className="flex min-h-screen flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 p-6">
                <div className="w-full max-w-md overflow-hidden rounded-xl bg-white shadow-lg">
                    <div className="bg-indigo-600 p-6 text-center">
                        <h1 className="text-3xl font-bold text-white">Welcome</h1>
                    </div>

                    <div className="p-8 text-center">
                        {auth.user ? (
                            <div className="space-y-6">
                                <p className="text-lg text-gray-700">
                                    You're logged in as <span className="font-semibold">{auth.user.name}</span>
                                </p>
                                <Link
                                    href="/dashboard"
                                    className="inline-block rounded-lg bg-indigo-600 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
                                >
                                    Go to Dashboard
                                </Link>
                            </div>
                        ) : (
                            <div className="space-y-6">
                                <p className="text-lg text-gray-700">You're not logged in. Please sign in to access your dashboard.</p>
                                <div className="flex flex-col justify-center gap-4 sm:flex-row">
                                    <Link
                                        href="/login"
                                        className="inline-block rounded-lg bg-indigo-600 px-6 py-3 font-medium text-white transition-colors duration-200 hover:bg-indigo-700"
                                    >
                                        Login
                                    </Link>
                                    <Link
                                        href="/register"
                                        className="inline-block rounded-lg border border-indigo-600 bg-white px-6 py-3 font-medium text-indigo-600 transition-colors duration-200 hover:bg-indigo-50"
                                    >
                                        Register
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>

                <footer className="mt-8 text-center text-sm text-gray-500">
                    &copy; {new Date().getFullYear()} Your Company Name. All rights reserved.
                </footer>
            </div>
        </>
    );
}
