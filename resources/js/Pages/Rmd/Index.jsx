import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { __ } from '@/Utils/lang';

export default function RmdIndex({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{__('RMD (Rencana Masa Depanku)')}</h2>}
        >
            <Head title="RMD" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="prose dark:prose-invert max-w-none">
                                <p className="mb-4 text-lg leading-relaxed">
                                    Hai, para pemegang masa depan! Kira-kira dalam sepuluh tahun ke depan kamu sudah bisa apa saja? Bekerja di mana? Bertemu dengan siapa saja? Atau sudah pergi ke kota mana saja? Kalau kamu mendapatkan buku ini pada usia yang ke-12, maka dalam sepuluh tahun yang akan datang usiamu 22 tahun. Kira kira pada saat itu seberapa banyak kamu sudah bertumbuh sesuai dengan rencana dan rancangan Tuhan dalam hidupmu?
                                </p>
                                <p className="mb-4 text-lg leading-relaxed">
                                    Buku ini akan membantumu memiliki rencana untuk mencapai semua mimpimu di masa depan. Namun buku ini akan menjadi lebih efektif dan berdaya guna, jika kamu memiliki seorang mentor yang membantumu dalam berproses, karena untuk mewujudkan mimpi masa depanmu tentu tidak mudah. Mentor akan membantumu dalam membuat rencana dan menjalankan semua rencanamu.
                                </p>
                                <p className="mb-8 text-lg leading-relaxed">
                                    Perjalanan menuju masa depan baru saja dimulai, dan perjalanan ini akan menjadi berharga pada saat kamu melibatkan Tuhan Yesus di dalamnya. Dengan demikian kamu dapat mencapai kepenuhan rencana Tuhan dalam hidupmu. Selamat berjuang sampai masa depan yang direncanakan Tuhan tercapai.
                                </p>
                                
                                <div className="mt-8 flex justify-center">
                                    <Link
                                        href={route('rmd.intro')}
                                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    >
                                        {__('Pengenalan RMD')}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
