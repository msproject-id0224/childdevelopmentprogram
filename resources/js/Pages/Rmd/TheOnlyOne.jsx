import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';
import { __ } from '@/Utils/lang';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import TextArea from '@/Components/TextArea';
import PrimaryButton from '@/Components/PrimaryButton';
import InputError from '@/Components/InputError';
import { Transition } from '@headlessui/react';
import { EyeIcon, SpeakerWaveIcon, HandRaisedIcon } from '@heroicons/react/24/outline';

export default function TheOnlyOne({ auth, theOnlyOne }) {
    const { data, setData, post, processing, errors, recentlySuccessful } = useForm({
        unique_traits: theOnlyOne?.unique_traits || '',
        current_education_level: theOnlyOne?.current_education_level || '',
        favorite_subject: theOnlyOne?.favorite_subject || '',
        favorite_subject_reason: theOnlyOne?.favorite_subject_reason || '',
        least_favorite_subject: theOnlyOne?.least_favorite_subject || '',
        least_favorite_subject_reason: theOnlyOne?.least_favorite_subject_reason || '',
        highest_score_subject: theOnlyOne?.highest_score_subject || '',
        highest_score_value: theOnlyOne?.highest_score_value || '',
        lowest_score_subject: theOnlyOne?.lowest_score_subject || '',
        lowest_score_value: theOnlyOne?.lowest_score_value || '',
        visual_checklist: Array.isArray(theOnlyOne?.visual_checklist) && typeof theOnlyOne?.visual_checklist[0] === 'object' 
            ? theOnlyOne?.visual_checklist.reduce((acc, curr) => ({...acc, [curr.index]: curr.score}), {}) 
            : (typeof theOnlyOne?.visual_checklist === 'object' && !Array.isArray(theOnlyOne?.visual_checklist) ? theOnlyOne?.visual_checklist : {}),
        auditory_checklist: Array.isArray(theOnlyOne?.auditory_checklist) && typeof theOnlyOne?.auditory_checklist[0] === 'object'
            ? theOnlyOne?.auditory_checklist.reduce((acc, curr) => ({...acc, [curr.index]: curr.score}), {})
            : (typeof theOnlyOne?.auditory_checklist === 'object' && !Array.isArray(theOnlyOne?.auditory_checklist) ? theOnlyOne?.auditory_checklist : {}),
        kinesthetic_checklist: Array.isArray(theOnlyOne?.kinesthetic_checklist) && typeof theOnlyOne?.kinesthetic_checklist[0] === 'object'
            ? theOnlyOne?.kinesthetic_checklist.reduce((acc, curr) => ({...acc, [curr.index]: curr.score}), {})
            : (typeof theOnlyOne?.kinesthetic_checklist === 'object' && !Array.isArray(theOnlyOne?.kinesthetic_checklist) ? theOnlyOne?.kinesthetic_checklist : {}),
        learned_aspects: theOnlyOne?.learned_aspects || '',
        aspects_to_improve: theOnlyOne?.aspects_to_improve || '',
    });

    const visualItems = [
        { no: 1, ciri: "Gaya Belajar", keterangan: "Melalui pengamatan, mengamati peragaan" },
        { no: 2, ciri: "Membaca", keterangan: "Menyukai deskripsi, sehingga sering kali di tengah-tengah membaca berhenti untuk membayangkan apa yang dibacanya" },
        { no: 3, ciri: "Mengeja", keterangan: "Mengenali huruf melalui rangkaian kata yang tertulis" },
        { no: 4, ciri: "Menulis", keterangan: "Hasil tulisan cenderung baik, terbaca jelas, dan rapi" },
        { no: 5, ciri: "Ingatan", keterangan: "Mudah mengingat wajah atau sesuatu tetapi lupa namanya, selalu menulis apa saja" },
        { no: 6, ciri: "Imajinasi", keterangan: "Memiliki imajinasi kuat dengan melihat detail dari gambar yang ada" },
        { no: 7, ciri: "Distraktibilitas", keterangan: "Lebih mudah terpecah perhatiannya jika ada gambar" },
        { no: 8, ciri: "Pemecahan", keterangan: "Menulis semua hal yang dipikirkan dalam suatu daftar" },
        { no: 9, ciri: "Respons ketika tidak ada aktivitas", keterangan: "Jalan-jalan melihat sesuatu yang dapat dilihat" },
        { no: 10, ciri: "Respons untuk situasi baru", keterangan: "Melihat sekeliling dengan mengamati struktur" },
        { no: 11, ciri: "Emosi", keterangan: "Mudah menangis dan marah, tampil ekspresif" },
        { no: 12, ciri: "Komunikasi", keterangan: "Tenang, tidak banyak bicara panjang, tidak sabaran ketika mendengar, lebih banyak mengamati" },
        { no: 13, ciri: "Penampilan", keterangan: "Rapi, paduan warna senada, dan suka urutan" },
        { no: 14, ciri: "Respons terhadap seni", keterangan: "Apresiasi terhadap seni apa saja yang dilihatnya secara mendalam dengan detail dan komponen, daripada karya secara keseluruhan" },
    ];

    const auditoryItems = [
        { no: 1, ciri: "Gaya Belajar", keterangan: "Belajar melalui instruksi dari orang lain" },
        { no: 2, ciri: "Membaca", keterangan: "Menikmati percakapan dan tidak mempedulikan ilustrasi gambar yang ada" },
        { no: 3, ciri: "Mengeja", keterangan: "Menggunakan pendekatan melalui bunyi kata" },
        { no: 4, ciri: "Menulis", keterangan: "Hasil tulisan cenderung tipis, seadanya" },
        { no: 5, ciri: "Ingatan", keterangan: "Mudah mengingat nama, tetapi lupa muka atau bentuknya, ingatan melalui pengulangan" },
        { no: 6, ciri: "Imajinasi", keterangan: "Tidak mengutamakan detail, lebih berpikir mengandalkan pendengaran" },
        { no: 7, ciri: "Distraktibilitas", keterangan: "Mudah terpecah perhatiannya dengan suara" },
        { no: 8, ciri: "Pemecahan", keterangan: "Pemecahan masalah melalui lisan" },
        { no: 9, ciri: "Respons ketika tidak ada aktivitas", keterangan: "Ngobrol atau bicara sendiri" },
        { no: 10, ciri: "Respons untuk situasi baru", keterangan: "Bicara tentang pro dan kontra" },
        { no: 11, ciri: "Emosi", keterangan: "Berteriak jika bahagia, mudah meledak-ledak tetapi cepat reda, emosi tergambar jelas melalui perubahan besarnya nada suara, dan tinggi rendahnya nada" },
        { no: 12, ciri: "Komunikasi", keterangan: "Senang mendengar dan cenderung repetitif dalam menjelaskan" },
        { no: 13, ciri: "Penampilan", keterangan: "Tidak memperhatikan harmonisasi paduan warna dalam penampilan" },
        { no: 14, ciri: "Respons terhadap seni", keterangan: "Lebih memilih musik. Kurang tertarik seni visual, namun siap berdiskusi sebagai karya secara keseluruhan, tidak berbicara secara detail dan komponen yang dilihatnya" },
    ];

    const kinestheticItems = [
        { no: 1, ciri: "Gaya Belajar", keterangan: "Belajar melalui aktivitas fisik, banyak bergerak, menyentuh" },
        { no: 2, ciri: "Membaca", keterangan: "Menunjuk tulisan dengan jari saat membaca, menyukai buku yang berorientasi pada plot" },
        { no: 3, ciri: "Mengeja", keterangan: "Mengeja melalui tulisan tangan atau membayangkan gerakan menulis" },
        { no: 4, ciri: "Menulis", keterangan: "Tulisan mungkin kurang rapi, tebal, dan menekan alat tulis dengan kuat" },
        { no: 5, ciri: "Ingatan", keterangan: "Mengingat apa yang dilakukan, bukan yang dilihat atau didengar" },
        { no: 6, ciri: "Imajinasi", keterangan: "Imajinasi melibatkan gerakan atau tindakan fisik" },
        { no: 7, ciri: "Distraktibilitas", keterangan: "Mudah terganggu oleh situasi yang tidak nyaman secara fisik" },
        { no: 8, ciri: "Pemecahan", keterangan: "Memecahkan masalah dengan mempraktikkan atau melakukan simulasi" },
        { no: 9, ciri: "Respons ketika tidak ada aktivitas", keterangan: "Gelisah, tidak bisa duduk diam, menggoyangkan kaki atau tangan" },
        { no: 10, ciri: "Respons untuk situasi baru", keterangan: "Mencoba melakukan sesuatu atau memanipulasi objek" },
        { no: 11, ciri: "Emosi", keterangan: "Mengekspresikan emosi melalui gerakan tubuh (melompat, memukul, memeluk)" },
        { no: 12, ciri: "Komunikasi", keterangan: "Menggunakan banyak bahasa tubuh, gestur, berdiri dekat lawan bicara" },
        { no: 13, ciri: "Penampilan", keterangan: "Lebih mementingkan kenyamanan daripada gaya, pakaian yang longgar" },
        { no: 14, ciri: "Respons terhadap seni", keterangan: "Menyukai seni yang melibatkan fisik (tari, drama, patung)" },
    ];

    const handleScoreChange = (type, index, score) => {
        const currentList = { ...data[`${type}_checklist`] };
        currentList[index] = score;
        setData(`${type}_checklist`, currentList);
    };

    const calculateTotalScore = (checklist) => {
        if (!checklist) return 0;
        return Object.values(checklist).reduce((acc, curr) => acc + parseInt(curr || 0), 0);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('rmd.the-only-one.store'), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">RMD - Satu-Satunya</h2>}
        >
            <Head title="RMD - Satu-Satunya" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            
                            {/* Header Section */}
                            <div className="text-center mb-8 border-b-2 border-orange-200 dark:border-orange-800 pb-4">
                                <h1 className="text-3xl font-bold text-orange-600 dark:text-orange-400">SATU-SATUNYA</h1>
                                <p className="text-lg italic text-gray-600 dark:text-gray-400 mt-2">PERTEMUAN 1</p>
                            </div>

                            <form onSubmit={submit} className="space-y-8">
                                
                                {/* Pembuka Section */}
                                <section>
                                    <h3 className="text-xl font-bold mb-4">Pembuka</h3>
                                    <div className="prose dark:prose-invert max-w-none mb-6 text-gray-700 dark:text-gray-300">
                                        <p className="mb-4">
                                            Bagikanlah cerita tentang dirimu dan keunikanmu kepada teman-temanmu.
                                        </p>
                                        <p className="mb-4">
                                            Pernahkah kamu menonton ajang pencarian bakat, misalnya <em>America's Got Talent</em> (AGT)?
                                            Pada acara seperti AGT tersebut, para peserta menampilkan keahlian dan kemampuan terbaik yang mereka miliki.
                                            Setiap peserta memiliki keunikan khas yang tidak dimiliki oleh peserta lainnya. Setiap manusia memang dikaruniai Tuhan keunikannya masing-masing.
                                            Ada yang bersifat turunan (bakat bawaan), ada yang diperoleh dari latihan, dan ada pula yang berasal dari perpaduan keduanya (bakat bawaan + latihan).
                                        </p>
                                        <p className="font-semibold text-lg text-orange-600 dark:text-orange-400">
                                            Apa saja keunikan-keunikan yang kamu miliki?
                                        </p>
                                    </div>
                                    <div className="mb-6">
                                        <InputLabel htmlFor="unique_traits" value="Tuliskan keunikanmu di sini" />
                                        <TextArea
                                            id="unique_traits"
                                            className="mt-1 block w-full"
                                            value={data.unique_traits}
                                            onChange={(e) => setData('unique_traits', e.target.value)}
                                            rows={4}
                                            placeholder="Saya unik karena..."
                                        />
                                        <InputError message={errors.unique_traits} className="mt-2" />
                                    </div>
                                    <div className="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                                        <p>
                                            Melalui buku RMD ini, kamu akan dibawa untuk lebih mendalami apa saja keunikan yang kamu miliki.
                                            Jangan lewatkan setiap pertemuannya, supaya kamu tidak ketinggalan materi-materinya.
                                        </p>
                                    </div>
                                </section>



                                {/* Penutup Section */}
                                <section className="border-t-2 border-gray-200 dark:border-gray-700 pt-8 mt-8">
                                    <h3 className="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Penutup</h3>
                                    <p className="mb-6 text-gray-700 dark:text-gray-300">
                                        Ingat kembali apa saja yang telah kita bahas bersama hari ini terkait perkembanganmu dalam aspek intelektual, kemudian tuliskan kesimpulan dan rencanamu dalam tabel berikut ini.
                                    </p>

                                    <div className="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg mb-8">
                                        <table className="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                            <thead className="bg-cyan-400 dark:bg-cyan-800">
                                                <tr>
                                                    <th scope="col" className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 w-16">No</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white w-1/3">Perkembangan Aspek Intelektualku dan Gaya Belajarku</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Jawaban</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6 align-top">1</td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-300 align-top">
                                                        Hal yang kupelajari
                                                    </td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                        <TextArea
                                                            id="learned_aspects"
                                                            className="mt-1 block w-full"
                                                            value={data.learned_aspects}
                                                            onChange={(e) => setData('learned_aspects', e.target.value)}
                                                            rows={3}
                                                            placeholder="Tuliskan jawabanmu di sini..."
                                                        />
                                                        <InputError message={errors.learned_aspects} className="mt-2" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6 align-top">2</td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-300 align-top">
                                                        Apa saja yang ingin kuperbaiki
                                                    </td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                                        <TextArea
                                                            id="aspects_to_improve"
                                                            className="mt-1 block w-full"
                                                            value={data.aspects_to_improve}
                                                            onChange={(e) => setData('aspects_to_improve', e.target.value)}
                                                            rows={3}
                                                            placeholder="Tuliskan jawabanmu di sini..."
                                                        />
                                                        <InputError message={errors.aspects_to_improve} className="mt-2" />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <p className="mb-4 text-gray-700 dark:text-gray-300">
                                        Mari kita berdoa mengucap syukur kepada Tuhan atas kemampuan intelektual yang Tuhan karuniakan kepada setiap kita.
                                    </p>
                                    
                                    <div className="mb-6 text-gray-700 dark:text-gray-300">
                                        <p className="mb-2">Selamat, kamu sudah mempelajari:</p>
                                        <ul className="list-disc list-inside space-y-1 pl-4">
                                            <li>Perkembangan aspek intelektualmu</li>
                                            <li>Jenis-jenis gaya belajar</li>
                                        </ul>
                                    </div>
                                </section>

                                {/* Proyek Rame-Rame Section */}
                                <section className="border-t-2 border-gray-200 dark:border-gray-700 pt-8 mt-8">
                                    <h3 className="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Proyek Rame-Rame</h3>
                                    <ul className="list-disc list-inside space-y-2 pl-4 text-gray-700 dark:text-gray-300">
                                        <li>Hafalkan Mazmur 139:13-14.</li>
                                        <li>Kumpulkanlah beberapa informasi tambahan mengenai gaya belajarmu untuk disampaikan kepada mentor dan temanmu pada pertemuan selanjutnya.</li>
                                    </ul>
                                </section>

                                {/* Kemajuan Pribadiku Section */}
                                <section className="bg-orange-50 dark:bg-gray-700 p-6 rounded-lg">
                                    <h3 className="text-xl font-bold mb-4">Kemajuan Pribadiku Dalam Empat Aspek Pengembangan</h3>
                                    <p className="mb-4 text-gray-700 dark:text-gray-300">
                                        Pusat Pengembangan Anak (PPA) di mana kamu terdaftar, telah merancang berbagai kegiatan untuk mengembangkan remaja sepertimu melalui empat aspek pengembangan.
                                        Keempat aspek itu adalah Intelektual, Sosio-Emosional, Fisik, dan Rohani. Aspek Intelektual akan kamu pelajari dalam Pertemuan 1-2, sedangkan aspek lainnya pada Pertemuan 3.
                                    </p>
                                    <p className="text-gray-700 dark:text-gray-300">
                                        Kegiatan-kegiatan PPA yang kamu ikuti tentu akan menolongmu bertumbuh menjadi pribadi yang semakin baik seiring berjalannya waktu.
                                        Sebagai langkah awal penemuan keunikan dirimu, cobalah menggunakan pertanyaan-pertanyaan berikut untuk melihat keadaan pribadimu.
                                        Kita mulai dari aspek yang pertama, yaitu intelektual.
                                    </p>
                                </section>

                                {/* Perkembanganku dalam aspek intelektual Section */}
                                <section>
                                    <h3 className="text-xl font-bold mb-6">Perkembanganku dalam aspek intelektual (berdasarkan nilai rapor 1 tahun terakhir)</h3>
                                    
                                    <div className="space-y-6">
                                        {/* Question 1 */}
                                        <div>
                                            <InputLabel htmlFor="current_education_level" value="Tingkat pendidikanku saat ini adalah" />
                                            <TextInput
                                                id="current_education_level"
                                                className="mt-1 block w-full"
                                                value={data.current_education_level}
                                                onChange={(e) => setData('current_education_level', e.target.value)}
                                            />
                                        </div>

                                        {/* Question 2 */}
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel htmlFor="favorite_subject" value="Pelajaran sekolah yang paling aku sukai adalah" />
                                                <TextInput
                                                    id="favorite_subject"
                                                    className="mt-1 block w-full"
                                                    value={data.favorite_subject}
                                                    onChange={(e) => setData('favorite_subject', e.target.value)}
                                                />
                                            </div>
                                            <div>
                                                <InputLabel htmlFor="favorite_subject_reason" value="Alasannya" />
                                                <TextArea
                                                    id="favorite_subject_reason"
                                                    className="mt-1 block w-full"
                                                    value={data.favorite_subject_reason}
                                                    onChange={(e) => setData('favorite_subject_reason', e.target.value)}
                                                    rows={1}
                                                />
                                            </div>
                                        </div>

                                        {/* Question 3 */}
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel htmlFor="least_favorite_subject" value="Pelajaran sekolah yang tidak aku sukai adalah" />
                                                <TextInput
                                                    id="least_favorite_subject"
                                                    className="mt-1 block w-full"
                                                    value={data.least_favorite_subject}
                                                    onChange={(e) => setData('least_favorite_subject', e.target.value)}
                                                />
                                            </div>
                                            <div>
                                                <InputLabel htmlFor="least_favorite_subject_reason" value="Alasannya" />
                                                <TextArea
                                                    id="least_favorite_subject_reason"
                                                    className="mt-1 block w-full"
                                                    value={data.least_favorite_subject_reason}
                                                    onChange={(e) => setData('least_favorite_subject_reason', e.target.value)}
                                                    rows={1}
                                                />
                                            </div>
                                        </div>

                                        {/* Question 4 */}
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel htmlFor="highest_score_subject" value="Nilai tertinggi di raporku adalah mata pelajaran" />
                                                <TextInput
                                                    id="highest_score_subject"
                                                    className="mt-1 block w-full"
                                                    value={data.highest_score_subject}
                                                    onChange={(e) => setData('highest_score_subject', e.target.value)}
                                                />
                                            </div>
                                            <div>
                                                <InputLabel htmlFor="highest_score_value" value="Nilainya" />
                                                <TextInput
                                                    id="highest_score_value"
                                                    className="mt-1 block w-full"
                                                    value={data.highest_score_value}
                                                    onChange={(e) => setData('highest_score_value', e.target.value)}
                                                />
                                            </div>
                                        </div>

                                        {/* Question 5 */}
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel htmlFor="lowest_score_subject" value="Nilai terendah di raporku adalah mata pelajaran" />
                                                <TextInput
                                                    id="lowest_score_subject"
                                                    className="mt-1 block w-full"
                                                    value={data.lowest_score_subject}
                                                    onChange={(e) => setData('lowest_score_subject', e.target.value)}
                                                />
                                            </div>
                                            <div>
                                                <InputLabel htmlFor="lowest_score_value" value="Nilainya" />
                                                <TextInput
                                                    id="lowest_score_value"
                                                    className="mt-1 block w-full"
                                                    value={data.lowest_score_value}
                                                    onChange={(e) => setData('lowest_score_value', e.target.value)}
                                                />
                                            </div>
                                        </div>

                                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-4 italic">
                                            Lengkapilah profil pribadimu di bagian aktivitas sekolah (mata pelajaran paling disukai).
                                            Oiya, jika nilai rapormu belum terlalu memuaskan, tidak perlu sedih, ya. Kamu masih punya waktu untuk belajar lebih tekun lagi agar mendapatkan nilai yang lebih baik di tahun ini.
                                            Agar lebih efektif dalam belajar, ada baiknya kamu mengetahui <strong>Tipe Gaya Belajar</strong> yang kamu miliki.
                                        </p>
                                    </div>
                                </section>

                                {/* Gaya Belajar Intro */}
                                <section>
                                    <h3 className="text-xl font-bold mb-4">Gaya Belajar</h3>
                                    <p className="mb-4 text-gray-700 dark:text-gray-300">
                                        Gaya belajar adalah cara yang paling disukai dan yang paling efektif bagi seseorang untuk menyerap informasi, mengingat dan mengolahnya.
                                        Memahami gaya belajar akan membantu kita mengetahui cara-cara belajar yang paling sesuai dengan diri kita sehingga kita bisa belajar dengan efektif.
                                    </p>
                                    <p className="mb-6 text-gray-700 dark:text-gray-300">
                                        Bobbi DePorter dan Mike Hernacki di dalam bukunya "Quantum Learning" menyebutkan bahwa gaya belajar setiap orang dibagi ke dalam 3 macam, yakni gaya belajar visual, auditori, dan kinestetik.
                                    </p>
                                    
                                    <div className="flex justify-around items-center mb-8">
                                        <div className="text-center">
                                            <div className="bg-yellow-100 dark:bg-yellow-900 rounded-full p-4 mb-2 inline-block">
                                                <EyeIcon className="h-12 w-12 text-yellow-600 dark:text-yellow-400" />
                                            </div>
                                            <div className="font-bold text-lg">VISUAL</div>
                                        </div>
                                        <div className="text-center">
                                            <div className="bg-red-100 dark:bg-red-900 rounded-full p-4 mb-2 inline-block">
                                                <SpeakerWaveIcon className="h-12 w-12 text-red-600 dark:text-red-400" />
                                            </div>
                                            <div className="font-bold text-lg">AUDITORY</div>
                                        </div>
                                        <div className="text-center">
                                            <div className="bg-green-100 dark:bg-green-900 rounded-full p-4 mb-2 inline-block">
                                                <HandRaisedIcon className="h-12 w-12 text-green-600 dark:text-green-400" />
                                            </div>
                                            <div className="font-bold text-lg">KINESTHETIC</div>
                                        </div>
                                    </div>

                                    <div className="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg mb-8">
                                        <p className="font-semibold mb-2">Instruksi:</p>
                                        <ol className="list-decimal list-inside space-y-1 text-gray-700 dark:text-gray-300">
                                            <li>Sediakan pensil dan penghapus untuk mengisi (di sini cukup klik/tap).</li>
                                            <li>Berilah tanda centang pada nomor pernyataan di bawah ini yang paling sesuai dengan dirimu!</li>
                                            <li>Jumlahkan semua tanda centang pada masing-masing kecerdasan. Itulah total skor untuk masing-masing kecerdasan tersebut.</li>
                                        </ol>
                                    </div>
                                </section>

                                {/* Visual Quiz */}
                                <section className="border-t-4 border-yellow-400 pt-6">
                                    <h4 className="text-lg font-bold mb-4 text-yellow-700 dark:text-yellow-400">A. VISUAL (BELAJAR DENGAN CARA MELIHAT)</h4>
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-yellow-50 dark:bg-yellow-900/20">
                                                <tr>
                                                    <th scope="col" className="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-8">No</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-48">Ciri</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">1</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">2</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">3</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">4</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">5</th>
                                                </tr>
                                            </thead>
                                            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                {visualItems.map((item, idx) => (
                                                    <tr key={idx}>
                                                        <td className="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{item.no}</td>
                                                        <td className="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{item.ciri}</td>
                                                        <td className="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{item.keterangan}</td>
                                                        {[1, 2, 3, 4, 5].map((score) => (
                                                            <td key={score} className="px-2 py-4 text-center whitespace-nowrap">
                                                                <input
                                                                    type="radio"
                                                                    name={`visual_${idx}`}
                                                                    className="text-yellow-600 focus:ring-yellow-500 h-4 w-4"
                                                                    checked={data.visual_checklist[idx] == score}
                                                                    onChange={() => handleScoreChange('visual', idx, score)}
                                                                />
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className="mt-4 text-right font-bold text-lg text-yellow-700 dark:text-yellow-400">
                                        Total Skor Visual: {calculateTotalScore(data.visual_checklist)}
                                    </div>
                                </section>

                                {/* Auditory Quiz */}
                                <section className="border-t-4 border-red-400 pt-6">
                                    <h4 className="text-lg font-bold mb-4 text-red-700 dark:text-red-400">B. AUDITORI (BELAJAR DENGAN CARA MENDENGAR)</h4>
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-red-50 dark:bg-red-900/20">
                                                <tr>
                                                    <th scope="col" className="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-8">No</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-48">Ciri</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">1</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">2</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">3</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">4</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">5</th>
                                                </tr>
                                            </thead>
                                            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                {auditoryItems.map((item, idx) => (
                                                    <tr key={idx}>
                                                        <td className="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{item.no}</td>
                                                        <td className="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{item.ciri}</td>
                                                        <td className="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{item.keterangan}</td>
                                                        {[1, 2, 3, 4, 5].map((score) => (
                                                            <td key={score} className="px-2 py-4 text-center whitespace-nowrap">
                                                                <input
                                                                    type="radio"
                                                                    name={`auditory_${idx}`}
                                                                    className="text-red-600 focus:ring-red-500 h-4 w-4"
                                                                    checked={data.auditory_checklist[idx] == score}
                                                                    onChange={() => handleScoreChange('auditory', idx, score)}
                                                                />
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className="mt-4 text-right font-bold text-lg text-red-700 dark:text-red-400">
                                        Total Skor Auditori: {calculateTotalScore(data.auditory_checklist)}
                                    </div>
                                </section>

                                {/* Kinesthetic Quiz */}
                                <section className="border-t-4 border-green-400 pt-6">
                                    <h4 className="text-lg font-bold mb-4 text-green-700 dark:text-green-400">C. KINESTETIK (BELAJAR DENGAN CARA BERGERAK, BEKERJA DAN MENYENTUH)</h4>
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-green-50 dark:bg-green-900/20">
                                                <tr>
                                                    <th scope="col" className="px-2 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-8">No</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-48">Ciri</th>
                                                    <th scope="col" className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">1</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">2</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">3</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">4</th>
                                                    <th scope="col" className="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10">5</th>
                                                </tr>
                                            </thead>
                                            <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                {kinestheticItems.map((item, idx) => (
                                                    <tr key={idx}>
                                                        <td className="px-2 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{item.no}</td>
                                                        <td className="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{item.ciri}</td>
                                                        <td className="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">{item.keterangan}</td>
                                                        {[1, 2, 3, 4, 5].map((score) => (
                                                            <td key={score} className="px-2 py-4 text-center whitespace-nowrap">
                                                                <input
                                                                    type="radio"
                                                                    name={`kinesthetic_${idx}`}
                                                                    className="text-green-600 focus:ring-green-500 h-4 w-4"
                                                                    checked={data.kinesthetic_checklist[idx] == score}
                                                                    onChange={() => handleScoreChange('kinesthetic', idx, score)}
                                                                />
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                    <div className="mt-4 text-right font-bold text-lg text-green-700 dark:text-green-400">
                                        Total Skor Kinestetik: {calculateTotalScore(data.kinesthetic_checklist)}
                                    </div>
                                </section>

                                {/* Summary & Reflection Section */}
                                <section className="border-t-2 border-gray-200 dark:border-gray-700 pt-8 mt-8">
                                    <h3 className="text-xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">Hasil Tes Gaya Belajar</h3>
                                    
                                    <div className="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg mb-8">
                                        <table className="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                            <thead className="bg-cyan-400 dark:bg-cyan-800">
                                                <tr>
                                                    <th scope="col" className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 w-16">No</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Gaya Belajar</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Skor</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">1</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">Visual</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300 font-bold">{calculateTotalScore(data.visual_checklist)}</td>
                                                </tr>
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">2</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">Auditori</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300 font-bold">{calculateTotalScore(data.auditory_checklist)}</td>
                                                </tr>
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">3</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">Kinestetik</td>
                                                    <td className="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300 font-bold">{calculateTotalScore(data.kinesthetic_checklist)}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div className="space-y-6 text-gray-700 dark:text-gray-300">
                                        <div>
                                            <h4 className="text-lg font-bold mb-2">Kegiatan kelompok:</h4>
                                            <ul className="list-disc list-inside space-y-2 pl-4">
                                                <li>Bersama dengan teman yang memiliki gaya belajar yang sama denganmu, baca dan pelajarilah karakteristik gaya belajar utamamu yang secara rinci tertulis dalam lembar tes yang baru saja kamu kerjakan.</li>
                                                <li>Diskusikanlah kesesuaian karakteristik tersebut dengan keadaanmu.</li>
                                            </ul>
                                        </div>

                                        <div>
                                            <h4 className="text-lg font-bold mb-2">Memaksimalkan Cara Belajar</h4>
                                            <p className="mb-4 leading-relaxed">
                                                Setelah kamu mengetahui gaya belajarmu, kamu bisa memikirkan berbagai cara yang tepat untuk memaksimalkan kegiatan belajarmu. Bersama dengan teman yang memiliki gaya belajar yang sama, coba lakukan survei untuk mendaftarkan cara-cara belajar yang sesuai dengan gaya belajarmu dan coba mulai menerapkannya ketika kamu belajar.
                                            </p>
                                            <p className="mb-4 leading-relaxed">
                                                Kamu bisa bertanya kepada mentor, membaca buku-buku referensi yang ada di perpustakaan PPA atau sekolah, mencari artikel di internet yang menjelaskan cara-cara belajar yang sesuai dengan gaya belajar utamamu. Catatlah baik-baik hasil survei yang kamu dapatkan dan ceritakanlah kepada teman-teman di kelompokmu pada pertemuan berikutnya.
                                            </p>
                                            <p className="mb-4 leading-relaxed">
                                                Semoga kamu dapat memaksimalkan jam-jam belajarmu dengan cara-cara belajar yang jitu sesuai dengan gaya belajarmu. Setelah kamu mempraktikkannya, coba rasakan, apakah kamu bisa lebih mudah dan lebih cepat memahami dan mengingat apa yang kamu pelajari? Bagikanlah pengalamanmu kepada teman di kelompokmu.
                                            </p>
                                        </div>
                                    </div>
                                </section>

                                {/* Penutup & Reflection Section */}
                                <section className="border-t-4 border-indigo-400 pt-6 mt-8">
                                    <h3 className="text-2xl font-bold mb-4 text-indigo-700 dark:text-indigo-400">PENUTUP</h3>
                                    
                                    <div className="prose dark:prose-invert max-w-none mb-6 text-gray-700 dark:text-gray-300">
                                        <p>
                                            Ingat kembali apa saja yang telah kita bahas bersama hari ini terkait perkembanganmu dalam aspek intelektual, kemudian tuliskan kesimpulan dan rencanamu dalam tabel berikut ini.
                                        </p>
                                    </div>

                                    <div className="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg mb-8">
                                        <table className="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                            <thead className="bg-indigo-50 dark:bg-indigo-900/20">
                                                <tr>
                                                    <th scope="col" className="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white sm:pl-6 w-16">No</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Perkembangan Aspek Intelektualku dan Gaya Belajarku</th>
                                                    <th scope="col" className="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Jawaban</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6 align-top">1</td>
                                                    <td className="px-3 py-4 text-sm text-gray-700 dark:text-gray-300 align-top font-medium">Hal yang kupelajari</td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <TextArea
                                                            id="learned_aspects"
                                                            className="mt-1 block w-full"
                                                            value={data.learned_aspects}
                                                            onChange={(e) => setData('learned_aspects', e.target.value)}
                                                            rows={3}
                                                            placeholder="Tuliskan hal yang kamu pelajari..."
                                                        />
                                                        <InputError message={errors.learned_aspects} className="mt-2" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td className="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6 align-top">2</td>
                                                    <td className="px-3 py-4 text-sm text-gray-700 dark:text-gray-300 align-top font-medium">Apa saja yang ingin kuperbaiki</td>
                                                    <td className="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <TextArea
                                                            id="aspects_to_improve"
                                                            className="mt-1 block w-full"
                                                            value={data.aspects_to_improve}
                                                            onChange={(e) => setData('aspects_to_improve', e.target.value)}
                                                            rows={3}
                                                            placeholder="Tuliskan apa yang ingin kamu perbaiki..."
                                                        />
                                                        <InputError message={errors.aspects_to_improve} className="mt-2" />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div className="bg-indigo-50 dark:bg-indigo-900/20 p-6 rounded-lg mb-8 border border-indigo-100 dark:border-indigo-800">
                                        <p className="text-lg text-indigo-800 dark:text-indigo-300 italic text-center font-medium">
                                            "Mari kita berdoa mengucap syukur kepada Tuhan atas kemampuan intelektual yang Tuhan karuniakan kepada setiap kita."
                                        </p>
                                    </div>

                                    <div className="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg mb-8 border border-green-100 dark:border-green-800">
                                        <h4 className="text-lg font-bold mb-2 text-green-800 dark:text-green-300">Selamat, kamu sudah mempelajari:</h4>
                                        <ul className="list-disc list-inside space-y-1 text-green-700 dark:text-green-400 ml-4">
                                            <li>Perkembangan aspek intelektualmu</li>
                                            <li>Jenis-jenis gaya belajar</li>
                                        </ul>
                                    </div>

                                    <div className="border-t-2 border-dashed border-gray-300 dark:border-gray-600 pt-6">
                                        <h4 className="text-xl font-bold mb-4 text-gray-800 dark:text-gray-200">Proyek Rame-Rame</h4>
                                        <ul className="list-decimal list-inside space-y-3 text-gray-700 dark:text-gray-300 ml-2">
                                            <li className="pl-2"><span className="font-bold">Hafalkan Mazmur 139:13-14.</span></li>
                                            <li className="pl-2">Kumpulkanlah beberapa informasi tambahan mengenai gaya belajarmu untuk disampaikan kepada mentor dan temanmu pada pertemuan selanjutnya.</li>
                                        </ul>
                                    </div>
                                </section>

                                {/* Action Buttons */}
                                <div className="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <Link href={route('rmd.true-success')} className="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                        &laquo; Kembali
                                    </Link>
                                    
                                    <div className="flex items-center">
                                        <Transition
                                            show={recentlySuccessful}
                                            enter="transition ease-in-out"
                                            enterFrom="opacity-0"
                                            leave="transition ease-in-out"
                                            leaveTo="opacity-0"
                                        >
                                            <p className="text-sm text-gray-600 dark:text-gray-400 mr-3">{__('Tersimpan.')}</p>
                                        </Transition>

                                        <PrimaryButton disabled={processing} className="mr-4">
                                            {__('Simpan')}
                                        </PrimaryButton>

                                        <Link href={route('rmd.the-only-one-meeting-2')} className="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Lanjut ke Pertemuan 2 &raquo;
                                        </Link>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
