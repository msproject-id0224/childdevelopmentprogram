import { Link } from '@inertiajs/react';
import { __ } from '@/Utils/lang';
import { useTheme } from '@/Hooks/useTheme';

export default function GuestLayout({ children }) {
    useTheme();

    return (
        <div className="relative min-h-screen flex flex-col items-center justify-center overflow-hidden">
            {/* Background with Blur */}
            <div 
                className="absolute inset-0 bg-cover bg-center"
                style={{ 
                    backgroundImage: "url('/assets/img/background.webp')",
                    filter: 'blur(5px)',
                    transform: 'scale(1.05)' // Reduced scale as blur is smaller
                }}
            ></div>

            {/* Overlay to darken slightly if needed */}
            <div className="absolute inset-0 bg-black/20 dark:bg-black/40 transition-colors duration-300"></div>

            <div className="relative z-10 w-full sm:max-w-md">
                <div className="overflow-hidden bg-white/50 dark:bg-gray-900/80 backdrop-blur-sm px-8 py-10 shadow-2xl sm:rounded-2xl border border-white/20 dark:border-gray-700 transition-colors duration-300">
                    <div className="flex flex-col items-center mb-8">
                        <Link href="/" className="flex flex-col items-center">
                            <img 
                                src="/assets/img/logo-rmd.png" 
                                alt={__('Logo RMD')} 
                                className="h-24 w-auto mb-4"
                            />
                            <h1 className="text-xl font-bold text-gray-800 dark:text-gray-100 text-center uppercase tracking-wider transition-colors duration-300">
                                {__('Child Development Program')}
                            </h1>
                        </Link>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
