import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link, router } from '@inertiajs/react';
import { __ } from '@/Utils/lang';
import PrimaryButton from '@/Components/PrimaryButton';
import { Transition } from '@headlessui/react';
import InputError from '@/Components/InputError';

export default function TheOnlyOneMeeting3({ auth, socioEmotional, files }) {
    const [isUploading, setIsUploading] = useState(false);
    const [uploadProgress, setUploadProgress] = useState(0);

    const { data, setData, post, processing, recentlySuccessful, errors } = useForm({
        learning_style_practice: socioEmotional?.learning_style_practice || '',
        learning_style_impact: socioEmotional?.learning_style_impact || '',
        birth_order_siblings: socioEmotional?.birth_order_siblings || '',
        parents_occupation: socioEmotional?.parents_occupation || '',
        home_responsibilities: socioEmotional?.home_responsibilities || '',
        family_uniqueness: socioEmotional?.family_uniqueness || '',
        extracurricular_activities: socioEmotional?.extracurricular_activities || '',
        ppa_activities: socioEmotional?.ppa_activities || '',
        hobbies: socioEmotional?.hobbies || '',
        strengths: socioEmotional?.strengths || '',
        weaknesses: socioEmotional?.weaknesses || '',
        reflection_learned: socioEmotional?.reflection_learned || '',
        reflection_improvement: socioEmotional?.reflection_improvement || '',
        height: socioEmotional?.height || '',
        weight: socioEmotional?.weight || '',
        physical_traits: socioEmotional?.physical_traits || '',
        favorite_sports: socioEmotional?.favorite_sports || '',
        sports_achievements: socioEmotional?.sports_achievements || '',
        eating_habits: socioEmotional?.eating_habits || '',
        sleeping_habits: socioEmotional?.sleeping_habits || '',
        health_issues: socioEmotional?.health_issues || '',
        physical_likes: socioEmotional?.physical_likes || '',
        physical_development_goal: socioEmotional?.physical_development_goal || '',
        spiritual_knowledge_jesus: socioEmotional?.spiritual_knowledge_jesus || '',
        spiritual_relationship_growth: socioEmotional?.spiritual_relationship_growth || '',
        spiritual_love_obedience: socioEmotional?.spiritual_love_obedience || '',
        spiritual_community: socioEmotional?.spiritual_community || '',
        spiritual_bible_study: socioEmotional?.spiritual_bible_study || '',
        spiritual_mentor: socioEmotional?.spiritual_mentor || '',
        spiritual_reflection_learned: socioEmotional?.spiritual_reflection_learned || '',
        spiritual_reflection_improvement: socioEmotional?.spiritual_reflection_improvement || '',
        chapter3_check1: !!socioEmotional?.chapter3_check1,
        chapter3_check2: !!socioEmotional?.chapter3_check2,
        chapter3_check3: !!socioEmotional?.chapter3_check3,
        chapter3_check4: !!socioEmotional?.chapter3_check4,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('rmd.the-only-one-meeting-3.store'), {
            preserveScroll: true,
        });
    };

    const handleFileUpload = (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setIsUploading(true);
        setUploadProgress(0);

        router.post(route('rmd.files.upload'), {
            file: file,
            meeting_type: 'the-only-one-meeting-3',
        }, {
            forceFormData: true,
            onProgress: (progress) => {
                setUploadProgress(progress.percentage);
            },
            onSuccess: () => {
                setIsUploading(false);
                setUploadProgress(0);
            },
            onError: () => {
                setIsUploading(false);
                setUploadProgress(0);
            },
        });
    };

    const deleteFile = (fileId) => {
        if (confirm('Apakah Anda yakin ingin menghapus file ini?')) {
            router.delete(route('rmd.files.delete', fileId));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">RMD - Pertemuan 3</h2>}
        >
            <Head title="RMD - Pertemuan 3" />

            <div className="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
                    {/* Header Card */}
                    <div className="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div className="bg-cyan-400 p-8 text-center text-white">
                            <h3 className="text-lg font-bold tracking-widest uppercase">BAB 3</h3>
                            <h1 className="text-4xl font-black mt-2">SATU-SATUNYA</h1>
                            <p className="text-xl italic mt-2 opacity-90">PERTEMUAN 3</p>
                        </div>

                        <div className="p-8 space-y-6">
                            <section>
                                <h4 className="text-2xl font-black text-gray-900 dark:text-white mb-4">Pembuka</h4>
                                <div className="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed text-lg">
                                    <p>
                                        Dalam dua pertemuan sebelumnya, kamu telah selesai mengenali dirimu dalam sisi aspek pengembangan intelektual. Kamu telah meneliti nilai-nilai rapormu selama di SD, menemukan gaya belajarmu dan juga kecerdasan majemukmu.
                                    </p>
                                    <div className="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-2xl border-l-4 border-orange-400 italic">
                                        "Apakah kamu telah mempraktikkan cara-cara belajar yang sesuai dengan gaya belajarmu? Apa dampak yang kamu rasakan? Jelaskan."
                                    </div>
                                    <textarea
                                        className="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 shadow-sm min-h-[100px]"
                                        placeholder="Tuliskan jawabanmu di sini..."
                                        value={data.learning_style_practice}
                                        onChange={e => setData('learning_style_practice', e.target.value)}
                                    />
                                    <textarea
                                        className="w-full rounded-2xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-cyan-500 focus:ring-cyan-500 shadow-sm min-h-[100px]"
                                        placeholder="Jelaskan dampaknya..."
                                        value={data.learning_style_impact}
                                        onChange={e => setData('learning_style_impact', e.target.value)}
                                    />
                                    <p>
                                        Tahun pertamamu di SLTP/SMP harus dimanfaatkan dengan sebaik mungkin untuk membangun kebiasaan belajar. Selain untuk mencapai prestasi akademik seoptimal mungkin, kebiasaan belajar akan membuat kamu menjadi orang yang berwawasan, dan membuatmu tidak akan kehilangan kesempatan-kesempatan emas di tahun-tahun yang akan datang.
                                    </p>
                                    <p>
                                        Sekarang kita akan melihat keunikanmu masing-masing dalam aspek lainnya yang tak kalah penting dari aspek intelektual.
                                    </p>
                                </div>
                            </section>

                            <hr className="border-gray-100 dark:border-gray-700" />

                            <section>
                                <h4 className="text-2xl font-black text-gray-900 dark:text-white mb-4 leading-tight">
                                    Perkembanganku dalam Aspek Fisik Selama Setahun Terakhir
                                </h4>
                                <p className="text-gray-700 dark:text-gray-300 text-lg mb-6 leading-relaxed">
                                    Perkembangan fisik tidak kalah penting dari perkembangan aspek lainnya. Apa pun yang kita lakukan, dilakukan melalui tubuh fisik kita. Jika fisik kita lemah atau sakit, maka kegiatan kita akan terganggu juga. Mari kita lihat bagaimana perkembangan fisikmu setahun terakhir dengan menjawab beberapa pertanyaan pada tabel di bawah ini.
                                </p>

                                <form onSubmit={submit} className="space-y-12">
                                    {/* Aspek Fisik */}
                                    <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                        <table className="w-full border-collapse">
                                            <thead>
                                                <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                    <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                    <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Pertanyaan</th>
                                                    <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                {[
                                                    { no: 1, q: 'Tinggi badanku', key: 'height' },
                                                    { no: 2, q: 'Berat badanku', key: 'weight' },
                                                    { no: 3, q: 'Ciri fisik lainnya', key: 'physical_traits' },
                                                    { no: 4, q: 'Olahraga kesukaanku', key: 'favorite_sports' },
                                                    { no: 5, q: 'Prestasiku di bidang olahraga', key: 'sports_achievements' },
                                                    { no: 6, q: 'Kebiasaan makanku', key: 'eating_habits' },
                                                    { no: 7, q: 'Kebiasaan tidurku', key: 'sleeping_habits' },
                                                    { no: 8, q: 'Gangguan kesehatan yang sering kualami', key: 'health_issues' },
                                                ].map((item) => (
                                                    <tr key={item.no}>
                                                        <td className="py-6 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">{item.no}</td>
                                                        <td className="py-6 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            {item.q}
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-24 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data[item.key]}
                                                                onChange={e => setData(item.key, e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>

                                    <div className="mt-8 space-y-6">
                                        <p className="text-gray-700 dark:text-gray-300 text-lg italic">
                                            Baca kembali jawaban-jawabanmu, kemudian jawablah pertanyaan berikut ini untuk menyimpulkan.
                                        </p>
                                        
                                        <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                            <table className="w-full border-collapse">
                                                <thead>
                                                    <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                        <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                        <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Perkembangan Aspek Fisik</th>
                                                        <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">1</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Apa yang paling kusukai dari perkembangan fisikku?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.physical_likes}
                                                                onChange={e => setData('physical_likes', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">2</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Apa yang ingin kukembangkan?<br />Bagaimana caranya?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.physical_development_goal}
                                                                onChange={e => setData('physical_development_goal', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <hr className="border-gray-100 dark:border-gray-700" />

                                    {/* Aspek Sosio-Emosional */}
                                    <div>
                                        <h4 className="text-2xl font-black text-gray-900 dark:text-white mb-4 leading-tight">
                                            Perkembanganku dalam Aspek Sosio-Emosional Selama Setahun Terakhir
                                        </h4>
                                        <p className="text-gray-700 dark:text-gray-300 text-lg mb-6">
                                            Aspek sosio-emosional adalah bagaimana kamu berelasi dengan dirimu sendiri dan juga orang lain, baik keluarga, teman-temanmu, serta orang dewasa lainnya.
                                        </p>
                                        <p className="text-gray-700 dark:text-gray-300 text-lg mb-8 italic">
                                            Isilah tabel berikut ini untuk mengenal keunikanmu dari aspek sosio-emosional.
                                        </p>

                                        <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                            <table className="w-full border-collapse">
                                                <thead>
                                                    <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                        <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                        <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Pertanyaan</th>
                                                        <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                    {[
                                                        { no: 1, q: 'Urutan lahir dan jumlah saudara', key: 'birth_order_siblings' },
                                                        { no: 2, q: 'Pekerjaan orang tuaku', key: 'parents_occupation' },
                                                        { no: 3, q: 'Pekerjaan yang jadi tanggung jawabku di rumah', key: 'home_responsibilities' },
                                                        { no: 4, q: 'Hal-hal unik di keluargaku', key: 'family_uniqueness' },
                                                        { no: 5, q: 'Kegiatan ekstrakurikuler yang kuikuti di sekolah', key: 'extracurricular_activities' },
                                                        { no: 6, q: 'Kegiatan yang kusukai di PPA', key: 'ppa_activities' },
                                                        { no: 7, q: 'Hobiku', key: 'hobbies' },
                                                        { no: 8, q: 'Kelebihanku', key: 'strengths' },
                                                        { no: 9, q: 'Kekuranganku', key: 'weaknesses' },
                                                    ].map((item) => (
                                                        <tr key={item.no}>
                                                            <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">{item.no}</td>
                                                            <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                                {item.q}
                                                            </td>
                                                            <td className="p-2">
                                                                <textarea
                                                                    className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                    value={data[item.key]}
                                                                    onChange={e => setData(item.key, e.target.value)}
                                                                    placeholder="Tuliskan di sini..."
                                                                />
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div className="mt-12 space-y-6">
                                        <p className="text-gray-700 dark:text-gray-300 text-lg italic">
                                            Baca kembali jawaban-jawabanmu, kemudian jawablah pertanyaan berikut ini untuk menyimpulkan.
                                        </p>
                                        
                                        <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                            <table className="w-full border-collapse">
                                                <thead>
                                                    <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                        <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                        <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Perkembangan Aspek Sosio-Emosional</th>
                                                        <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">1</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold italic">
                                                            Hal yang kupelajari
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.reflection_learned}
                                                                onChange={e => setData('reflection_learned', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">2</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold italic">
                                                            Apa yang ingin kukembangkan?<br />Bagaimana caranya?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.reflection_improvement}
                                                                onChange={e => setData('reflection_improvement', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <hr className="border-gray-100 dark:border-gray-700" />

                                    {/* Aspek Rohani */}
                                    <div>
                                        <h4 className="text-2xl font-black text-gray-900 dark:text-white mb-4 leading-tight">
                                            Perkembanganku dalam Aspek Rohani Selama Setahun Terakhir
                                        </h4>
                                        <div className="space-y-4 text-gray-700 dark:text-gray-300 leading-relaxed text-lg mb-8">
                                            <p>
                                                Ini aspek terakhir yang kita bahas, tetapi justru yang paling penting dan mendasari semua aspek lain. Segala kegiatan yang kita lakukan, baik itu kegiatan fisik, bersekolah, kegiatan sosial, dan lainnya, kita kerjakan sebagai bentuk kasih dan ketaatan kita kepada Tuhan. Itu artinya, aspek rohani menjadi pusat dan seharusnya menjadi penggerak bagi semua aspek lainnya. Jawablah beberapa pertanyaan pada tabel di bawah ini.
                                            </p>
                                        </div>

                                        <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                            <table className="w-full border-collapse">
                                                <thead>
                                                    <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                        <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                        <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Pertanyaan</th>
                                                        <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">1</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Sejauh mana aku mengenal Yesus secara pribadi?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_knowledge_jesus}
                                                                onChange={e => setData('spiritual_knowledge_jesus', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">2</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Bagaimana pertumbuhan relasiku secara pribadi dengan Tuhan?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_relationship_growth}
                                                                onChange={e => setData('spiritual_relationship_growth', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">3</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Sejauh apa kasihku kepada-Nya bertumbuh? Bagaimana aku bertumbuh dalam ketaatan kepada-Nya?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_love_obedience}
                                                                onChange={e => setData('spiritual_love_obedience', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">4</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Apakah aku memiliki komunitas yang menolongku bertumbuh secara rohani?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_community}
                                                                onChange={e => setData('spiritual_community', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">5</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Apakah aku mempelajari Alkitab secara teratur dan menyukainya?
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_bible_study}
                                                                onChange={e => setData('spiritual_bible_study', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">6</td>
                                                        <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                            Adakah orang dewasa yang menolongku untuk bertumbuh secara rohani? Jelaskan bagaimana mereka menolongmu bertumbuh!
                                                        </td>
                                                        <td className="p-2">
                                                            <textarea
                                                                className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                value={data.spiritual_mentor}
                                                                onChange={e => setData('spiritual_mentor', e.target.value)}
                                                                placeholder="Tuliskan di sini..."
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div className="mt-8 space-y-6">
                                            <div className="bg-cyan-50 dark:bg-cyan-900/20 p-8 rounded-3xl border-l-8 border-cyan-400">
                                                <h5 className="text-xl font-black text-cyan-700 dark:text-cyan-300 mb-4 uppercase tracking-wider">Penting untuk diperhatikan</h5>
                                                <div className="space-y-4 text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                                                    <p>
                                                        Keyakinan yang kamu pilih haruslah didasarkan pada pemahaman yang benar dan memadai, agar keyakinanmu kokoh dan kamu dapat menjalaninya dengan sepenuh hati. Jika saat ini kamu belum terlalu yakin, tidak mengapa. Ini justru adalah saat yang tepat untuk mulai mempelajari dan memahami dengan lebih keyakinanmu akan Tuhan dan kekristenan, agar kamu dapat sungguh-sungguh mengalami kehidupan yang penuh dengan kemenangan bersama dengan Tuhan.
                                                    </p>
                                                    <p className="italic font-medium">
                                                        Baca kembali jawaban-jawabanmu, kemudian jawablah pertanyaan berikut ini untuk menyimpulkan.
                                                    </p>
                                                </div>
                                            </div>

                                            <div className="border-2 border-orange-400 dark:border-orange-700 rounded-3xl overflow-hidden shadow-lg bg-white dark:bg-gray-800">
                                                <table className="w-full border-collapse">
                                                    <thead>
                                                        <tr className="bg-cyan-400 dark:bg-cyan-700 text-white">
                                                            <th className="py-4 px-4 border-r border-white/20 w-16 text-center font-bold">No</th>
                                                            <th className="py-4 px-6 border-r border-white/20 text-center font-bold">Perkembangan Aspek Rohani</th>
                                                            <th className="py-4 px-6 text-center font-bold">Jawaban</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="divide-y-2 divide-orange-400 dark:divide-orange-700 text-gray-800 dark:text-gray-200">
                                                        <tr>
                                                            <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">1</td>
                                                            <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                                Hal yang kupelajari
                                                            </td>
                                                            <td className="p-2">
                                                                <textarea
                                                                    className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                    value={data.spiritual_reflection_learned}
                                                                    onChange={e => setData('spiritual_reflection_learned', e.target.value)}
                                                                    placeholder="Tuliskan di sini..."
                                                                />
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td className="py-8 px-4 border-r-2 border-orange-400 dark:border-orange-700 text-center font-bold">2</td>
                                                            <td className="py-8 px-6 border-r-2 border-orange-400 dark:border-orange-700 font-medium">
                                                                Apa yang ingin kukembangkan? Bagaimana caranya?
                                                            </td>
                                                            <td className="p-2">
                                                                <textarea
                                                                    className="w-full h-32 border-none focus:ring-0 bg-transparent resize-none dark:text-gray-200"
                                                                    value={data.spiritual_reflection_improvement}
                                                                    onChange={e => setData('spiritual_reflection_improvement', e.target.value)}
                                                                    placeholder="Tuliskan di sini..."
                                                                />
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div className="mt-12 space-y-8">
                                            <div className="bg-orange-50 dark:bg-orange-900/20 p-8 rounded-3xl border-2 border-orange-400 dark:border-orange-700 space-y-6">
                                                <h5 className="text-2xl font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest">Penutup</h5>
                                                <div className="space-y-4 text-gray-700 dark:text-gray-300 text-lg leading-relaxed">
                                                    <p>
                                                        Cukup banyak informasi yang telah kamu kumpulkan terkait dirimu sendiri. Semoga kamu lebih mengenal bagaimana kondisi perkembanganmu secara holistik/menyeluruh. Lantas bagaimana perasaanmu setelah mengetahui semua informasi ini? Apakah kamu puas?
                                                    </p>
                                                    <p>
                                                        Kamu masih memiliki banyak kesempatan untuk mengembangkan dirimu melalui keempat aspek pengembangan tersebut. Lakukan rencana pengembangan yang telah kamu buat di bab ini dan lihatlah bagaimana kamu bisa membuat kemajuan di masing-masing bidang tersebut.
                                                    </p>
                                                    <p className="font-bold text-orange-600 dark:text-orange-400 italic">
                                                        Selamat! Kamu telah sampai pada bagian akhir dalam bab 3. Berilah tanda √ untuk hal-hal yang telah kamu selesaikan dalam bab ini.
                                                    </p>
                                                </div>

                                                <div className="grid grid-cols-1 gap-4 mt-6">
                                                    {[
                                                        { id: 'chapter3_check1', label: 'Mengetahui perkembanganmu dalam keempat aspek pengembangan.' },
                                                        { id: 'chapter3_check2', label: 'Mengetahui jenis gaya belajarmu.' },
                                                        { id: 'chapter3_check3', label: 'Memaksimalkan cara-cara belajar yang sesuai dengan gaya belajarmu.' },
                                                        { id: 'chapter3_check4', label: 'Mengetahui tiga jenis kecerdasan majemukmu yang utama.' },
                                                    ].map((item) => (
                                                        <label key={item.id} className="flex items-start space-x-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-orange-200 dark:border-orange-900 cursor-pointer hover:bg-orange-100 dark:hover:bg-orange-900/40 transition-colors">
                                                            <div className="relative flex items-center">
                                                                <input
                                                                    type="checkbox"
                                                                    className="w-6 h-6 rounded border-2 border-orange-400 text-orange-500 focus:ring-orange-500 dark:bg-gray-700 dark:border-orange-700"
                                                                    checked={data[item.id]}
                                                                    onChange={e => setData(item.id, e.target.checked)}
                                                                />
                                                            </div>
                                                            <span className="text-gray-700 dark:text-gray-300 font-medium">{item.label}</span>
                                                        </label>
                                                    ))}
                                                </div>
                                            </div>

                                            <div className="bg-cyan-50 dark:bg-cyan-900/20 p-8 rounded-3xl border-2 border-cyan-400 dark:border-cyan-700 space-y-6">
                                                <h5 className="text-2xl font-black text-cyan-600 dark:text-cyan-400 uppercase tracking-widest">Proyek Rame-Rame</h5>
                                                <ul className="space-y-4 text-gray-700 dark:text-gray-300 text-lg list-disc pl-6">
                                                    <li>
                                                        Bersama dengan teman sekelompokmu, pilihlah cara merayakan selesainya bab 3, yang menurut kalian paling asik, paling bermanfaat, dan sesuai dengan tema bab tersebut.
                                                    </li>
                                                    <li>
                                                        Ucapkan syukur kepada Tuhan atas perkembangan pribadi yang telah kamu capai sejauh ini.
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-end gap-4 mt-8">
                                        <Transition
                                            show={recentlySuccessful}
                                            enter="transition ease-in-out"
                                            enterFrom="opacity-0"
                                            leave="transition ease-in-out"
                                            leaveTo="opacity-0"
                                        >
                                            <p className="text-sm text-green-600 dark:text-green-400 font-medium">
                                                Jawaban berhasil disimpan.
                                            </p>
                                        </Transition>

                                        <PrimaryButton disabled={processing} className="bg-orange-500 hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700">
                                            Simpan Progres Bab 3
                                        </PrimaryButton>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>

                    {/* File Management Card */}
                    <div className="bg-white dark:bg-gray-800 p-8 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h4 className="text-2xl font-black text-gray-900 dark:text-white mb-6">Dokumen Pendukung</h4>
                        
                        <div className="space-y-6">
                            <div className="flex items-center gap-4">
                                <div className="flex-1">
                                    <label className="relative group cursor-pointer block">
                                        <div className="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-orange-400 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                                        <div className="relative flex items-center justify-center px-6 py-4 bg-white dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl group-hover:border-cyan-400 transition-colors">
                                            <div className="text-center">
                                                <p className="text-gray-600 dark:text-gray-400 font-medium">
                                                    {isUploading ? `Mengunggah... ${uploadProgress}%` : 'Klik untuk unggah foto/dokumen'}
                                                </p>
                                                <p className="text-xs text-gray-400 mt-1">Maksimal 10MB</p>
                                            </div>
                                        </div>
                                        <input 
                                            type="file" 
                                            className="hidden" 
                                            onChange={handleFileUpload}
                                            disabled={isUploading}
                                        />
                                    </label>
                                </div>
                            </div>

                            {files && files.length > 0 && (
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                                    {files.map((file) => (
                                        <div key={file.id} className="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800">
                                            <div className="flex items-center gap-3 overflow-hidden">
                                                <div className="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg text-cyan-600">
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="C9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                                                    {file.file_name}
                                                </span>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <a 
                                                    href={route('rmd.files.download', file.id)}
                                                    className="p-2 text-gray-400 hover:text-cyan-500 transition-colors"
                                                    title="Unduh"
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="C4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </a>
                                                <button 
                                                    onClick={() => deleteFile(file.id)}
                                                    className="p-2 text-gray-400 hover:text-red-500 transition-colors"
                                                    title="Hapus"
                                                >
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Navigation */}
                    <div className="flex justify-between items-center py-8">
                        <Link 
                            href={route('rmd.the-only-one-meeting-2')}
                            className="flex items-center gap-2 text-gray-500 hover:text-cyan-500 font-bold transition-colors"
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Pertemuan Sebelumnya
                        </Link>
                        
                        <div className="flex items-center gap-4">
                            <Link 
                                href={route('rmd.chapters')}
                                className="px-8 py-3 bg-white dark:bg-gray-800 border-2 border-cyan-400 text-cyan-500 rounded-full font-bold hover:bg-cyan-50 transition-colors shadow-sm"
                            >
                                Daftar Isi
                            </Link>

                            <Link 
                                href={route('rmd.career-exploration')}
                                className="flex items-center gap-2 px-8 py-3 bg-cyan-500 text-white rounded-full font-bold hover:bg-cyan-600 transition-colors shadow-lg shadow-cyan-200 dark:shadow-none"
                            >
                                Menentukan Cita-Cita
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
