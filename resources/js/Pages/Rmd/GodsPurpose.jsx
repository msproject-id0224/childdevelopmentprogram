import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { __ } from '@/Utils/lang';
import PrimaryButton from '@/Components/PrimaryButton';

export default function GodsPurpose({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{__('Tujuan Tuhan Bagimu')}</h2>}
        >
            <Head title="Tujuan Tuhan Bagimu" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100 space-y-8">
                            
                            {/* Header Section */}
                            <div className="text-center">
                                <h3 className="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">TUJUAN TUHAN BAGIMU</h3>
                                <div className="w-24 h-1 bg-indigo-500 mx-auto rounded"></div>
                            </div>

                            {/* Pembuka Section */}
                            <section className="prose dark:prose-invert max-w-none">
                                <h4 className="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">{__('Pembuka')}</h4>
                                <div className="bg-indigo-50 dark:bg-gray-700 p-6 rounded-lg shadow-inner mb-6">
                                    <p className="mb-4 leading-relaxed">
                                        Pernahkah kamu melihat pensil digunakan untuk mengikat rambut atau digunakan 
                                        untuk melempar? Mungkin beberapa darimu pernah melihatnya, tetapi apakah pensil 
                                        tersebut bisa berfungsi dengan maksimal jika bukan untuk tujuan menulis? Begitu pula 
                                        dengan hidup manusia, setiap manusia memiliki tujuan khusus untuk apa ia hidup di dunia.
                                    </p>
                                    <p className="mb-4 leading-relaxed">
                                        Jika kamu ingin tahu mengapa kamu ditempatkan di dunia ini, kamu harus 
                                        memulainya dengan Tuhan sebagai penciptamu. Kamu dilahirkan karena rencana-Nya 
                                        dan dengan tujuan tertentu. Memfokuskan pada diri sendiri tidak akan pernah membuat 
                                        kita mengenali maksud dan tujuan hidup kita.
                                    </p>
                                </div>
                                
                                <p className="mb-4 leading-relaxed">
                                    Kamu harus memulainya dengan Tuhan yang adalah penciptamu. Kamu diciptakan 
                                    oleh Tuhan dan untuk Tuhan. Pada saat Tuhan menciptakamu, dia punya rencana yang 
                                    besar bahkan melalui kamu Tuhan ingin menunjukkan karya dan kebesaran-Nya bagi 
                                    dunia ini.
                                </p>
                                <p className="mb-4 leading-relaxed">
                                    Mungkin kamu merasa apa yang telah kamu lalui bukanlah pengalaman yang 
                                    baik, tetapi sesungguhnya Tuhan punya rencana yang baik bagimu bahkan ketika kamu 
                                    sudah banyak mengalami pengalaman yang tidak baik.
                                </p>
                                <blockquote className="border-l-4 border-indigo-500 pl-4 italic text-gray-600 dark:text-gray-400 my-6">
                                    "Dalam bagian ini, kita akan bersama-sama melihat seberapa baik dan indah rencana 
                                    atau maksud Tuhan pada saat Dia menciptakanmu."
                                </blockquote>
                            </section>

                            {/* Pentingnya Tujuan Hidup Section */}
                            <section className="prose dark:prose-invert max-w-none">
                                <h4 className="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">{__('Pentingnya Tujuan Hidup')}</h4>
                                
                                <div className="flex flex-col md:flex-row items-center gap-6 mb-6">
                                    <div className="flex-1">
                                        <p className="mb-4 leading-relaxed">
                                            Thomas Carlyle, seorang ahli sejarah dari Skotlandia, pernah berkata, 
                                            <span className="font-semibold text-indigo-600 dark:text-indigo-400"> “Orang yang hidup tanpa tujuan itu seperti kapal tanpa kemudi.”</span>
                                        </p>
                                        <p className="mb-4 leading-relaxed">
                                            Tujuan hidup sangat penting, sama seperti ketika kamu berjalan di jalan raya lalu 
                                            bertemu dengan seorang teman yang sedang berjalan kaki dan kamu bertanya, “Mau ke 
                                            mana?” Lalu temanmu menjawab, “Saya hanya berjalan dan tidak tahu mau ke mana.” 
                                            Bukankah hal itu sangat aneh. Orang yang setiap hari berjalan tanpa tujuan adalah orang 
                                            yang akan sia-sia menjalani hidupnya.
                                        </p>
                                    </div>
                                    <div className="md:w-1/3 flex justify-center">
                                        <div className="text-9xl text-indigo-200 dark:text-indigo-900">
                                            🧭
                                        </div>
                                    </div>
                                </div>

                                <div className="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 p-6 rounded-lg">
                                    <p className="leading-relaxed">
                                        Ketika kamu mengetahui tujuan perjalananmu, maka biasanya kamu akan 
                                        mempersiapkan hal-hal penting yang akan kamu bawa untuk sampai pada tujuan 
                                        tersebut. Saat hendak pergi berlibur ke hutan atau pegunungan, tentu saja kamu 
                                        tidak perlu membawa pakaian renang atau peralatan menyelam. Demikian juga dalam 
                                        mempersiapkan masa depanmu, tentu saja Tuhan sudah menyediakan sebuah tujuan 
                                        yang baik dan indah bagi masa depanmu, namun kamu perlu membuat persiapan untuk 
                                        berjalan ke arah masa depan tersebut.
                                    </p>
                                </div>
                            </section>

                            {/* Navigation Buttons */}
                            <div className="flex justify-between items-center pt-8 border-t border-gray-200 dark:border-gray-700">
                                <Link
                                    href={route('rmd.profile')}
                                    className="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150"
                                >
                                    &laquo; {__('Kembali ke Profil')}
                                </Link>
                                
                                <Link href={route('rmd.what-the-bible-says')}>
                                    <PrimaryButton>
                                        {__('Lanjut: Apa Kata Alkitab')} &raquo;
                                    </PrimaryButton>
                                </Link>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
