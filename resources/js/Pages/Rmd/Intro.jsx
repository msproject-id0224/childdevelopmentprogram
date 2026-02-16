import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { __ } from '@/Utils/lang';

export default function RmdIntro({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{__('Pengenalan RMD')}</h2>}
        >
            <Head title="Pengenalan RMD" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="prose dark:prose-invert max-w-none">
                                <h3 className="text-2xl font-bold mb-6 text-center">{__('APA ITU BUKU RENCANA MASA DEPANKU')}</h3>
                                
                                <p className="mb-4 text-lg leading-relaxed">
                                    Seperti namanya, Buku Rencana Masa Depanku (RMD) adalah sebuah buku (dalam bentuk buku digital) yang dapat menjadi alat bantu untuk merencanakan masa depanmu. Melalui buku ini, kamu akan dibawa untuk memahami apa tujuan yang Tuhan rancang untuk hidupmu. Hidupmu sungguh berharga dan untuk itu perlu dijalankan dengan baik seperti yang dirancangkan Allah. Melalui buku ini, kamu dapat membangun mimpi akan masa depanmu dan mempersiapkan dirimu untuk mewujudkan impian itu.
                                </p>

                                <div className="my-6 pl-4 border-l-4 border-blue-500 italic text-gray-700 dark:text-gray-300">
                                    <p className="font-semibold">Apa impian yang ada dalam hatimu untuk masa depanmu?</p>
                                    <ul className="list-disc pl-5 mt-2 space-y-1">
                                        <li>Kamu mungkin bermimpi menjadi seorang politisi yang berpengaruh.</li>
                                        <li>Kamu mungkin bermimpi menolong orang di rumah sakit.</li>
                                        <li>Kamu mungkin bermimpi menjadi Lionel Messi berikutnya, sang pemain sepak bola yang terkenal itu.</li>
                                        <li>Kamu mungkin bermimpi menjadi seseorang yang dipakai oleh Tuhan untuk melayani orang lain.</li>
                                    </ul>
                                </div>

                                <p className="mb-4 text-lg leading-relaxed">
                                    Ini adalah kesempatanmu untuk membuat hidup yang Tuhan anugerahkan ini benar-benar berarti, bermanfaat bagi sesama dan memuliakan nama Tuhan. Bersiaplah untuk menemukan impian terbaikmu di dalam Tuhan, dan memberikan usaha terbaikmu agar impian itu tidak hanya sekedar angan-angan semata, tapi sungguh-sungguh bisa diwujudkan menjadi kenyataan.
                                </p>

                                <p className="mb-4 text-lg leading-relaxed">
                                    Buku ini terdiri dari dua bagian besar. Bagian pertama berisi profil pribadi dan daftar perencanaanmu. Sedangkan bagian kedua berisi tujuh bab pembelajaran yang akan memandumu membuat perencanaan masa depan setiap tahunnya. Ikutilah setiap pembelajaran di dalam buku ini dan lengkapilah profil pribadi dan perencanaanmu tahun demi tahun, agar peta perjalananmu menuju pulau impian yaitu cita-cita masa depanmu semakin lengkap setiap tahunnya.
                                </p>

                                <p className="mb-4 text-lg leading-relaxed">
                                    Menyelesaikan pengisian buku RMD ini tentu tidak berarti kamu langsung akan dapat mencapai pulau impianmu. Tentu dibutuhkan usahamu, disiplin dan kesetiaanmu untuk menjalankan perencanaan yang telah kamu buat. Ingatlah untuk selalu bersemangat sepanjang perjalanan menuju pulau impianmu. Gunakan perencanaanmu sebagai peta perjalananmu, agar kamu tidak tersesat dan segera tiba ditempat tujuan.
                                </p>

                                <p className="mb-4 text-lg leading-relaxed">
                                    Buku ini dirancang untuk dipelajari dan diisi secara perlahan dalam kelompok kecil dengan pendampingan seorang mentor. Akan ada banyak diskusi, berbagi pendapat, proyek bersama dan kegiatan pembelajaran lain yang akan kamu ikuti bersama dengan teman-temanmu. Kami berharap kamu dapat membangun persahabatan dengan sesama teman sepanjang perjalananmu membangun impian dan mewujudkannya.
                                </p>

                                <p className="mb-8 text-lg leading-relaxed font-medium">
                                    Semoga kamu dan semua teman-temanmu di PPA dapat menemukan dan memaksimalkan potensi yang telah Tuhan berikan dan bangkit menjadi remaja-remaja yang berkomitmen menjalankan tugas mulia yang sudah Tuhan percayakan. Selamat merencanakan masa depanmu bersama dengan Tuhan.
                                </p>

                                <div className="mt-8 flex justify-center">
                                    <Link
                                        href={route('rmd.profile')}
                                        className="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-bold text-base text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                                    >
                                        {__('Sudah Siap Mengisi RMD?')}
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
