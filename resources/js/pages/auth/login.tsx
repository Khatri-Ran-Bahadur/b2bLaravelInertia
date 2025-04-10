import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle, LockIcon, MailIcon } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type LoginForm = {
    email: string;
    password: string;
    remember: boolean;
};

interface LoginProps {
    status?: string;
    canResetPassword: boolean;
}

export default function Login({ status, canResetPassword }: LoginProps) {
    const { data, setData, post, processing, errors, reset } = useForm<Required<LoginForm>>({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <div className="min-h-screen flex">
            {/* Left Side - Background Gradient */}
            <div className="hidden lg:flex flex-1 bg-gradient-to-br from-blue-600 to-purple-600 items-center justify-center p-12">
                <div className="text-white text-center max-w-md">
                    <h1 className="text-4xl font-bold mb-6">Welcome Back</h1>
                    <p className="text-lg mb-8 opacity-80">
                        Log in to access your personalized dashboard and continue your journey with us.
                    </p>
                    <div className="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                        <p className="italic text-sm">"Seamless access to your world, right at your fingertips."</p>
                    </div>
                </div>
            </div>

            {/* Right Side - Login Form */}
            <div className="flex-1 flex items-center justify-center px-6 lg:px-8 bg-gray-50">
                <div className="w-full max-w-md space-y-8">
                    <div>
                        <h2 className="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                            Sign in to your account
                        </h2>
                        <p className="mt-2 text-center text-sm text-gray-600">
                            Or{' '}
                            <TextLink href={route('register')} className="font-medium text-blue-600 hover:text-blue-500">
                                create a new account
                            </TextLink>
                        </p>
                    </div>

                    <form className="mt-8 space-y-6" onSubmit={submit}>
                        <div className="rounded-md shadow-sm space-y-4">
                            {/* Email Input */}
                            <div>
                                <Label htmlFor="email" className="sr-only">Email address</Label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <MailIcon className="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="email"
                                        type="email"
                                        required
                                        autoFocus
                                        autoComplete="email"
                                        placeholder="Email address"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="pl-10"
                                    />
                                </div>
                                <InputError message={errors.email} />
                            </div>

                            {/* Password Input */}
                            <div>
                                <Label htmlFor="password" className="sr-only">Password</Label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <LockIcon className="h-5 w-5 text-gray-400" />
                                    </div>
                                    <Input
                                        id="password"
                                        type="password"
                                        required
                                        autoComplete="current-password"
                                        placeholder="Password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        className="pl-10"
                                    />
                                </div>
                                <InputError message={errors.password} />
                            </div>
                        </div>

                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <Checkbox
                                    id="remember"
                                    checked={data.remember}
                                    onClick={() => setData('remember', !data.remember)}
                                />
                                <Label htmlFor="remember" className="ml-2 block text-sm text-gray-900">
                                    Remember me
                                </Label>
                            </div>

                            {canResetPassword && (
                                <div className="text-sm">
                                    <TextLink 
                                        href={route('password.request')} 
                                        className="font-medium text-blue-600 hover:text-blue-500"
                                    >
                                        Forgot password?
                                    </TextLink>
                                </div>
                            )}
                        </div>

                        <div>
                            <Button 
                                type="submit" 
                                className="group relative w-full flex justify-center py-3"
                                disabled={processing}
                            >
                                {processing ? (
                                    <LoaderCircle className="h-5 w-5 animate-spin mr-2" />
                                ) : null}
                                Sign in
                            </Button>
                        </div>
                    </form>

                    {status && (
                        <div className="text-center text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md">
                            {status}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}