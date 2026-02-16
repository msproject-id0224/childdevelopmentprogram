import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import MentorScheduleTable from '@/Components/MentorScheduleTable';
import { __ } from '@/Utils/lang';
import { useState } from 'react';
import ScheduleTab from '@/Components/Dashboard/ScheduleTab';
import PhotoRequestsTab from '@/Components/Dashboard/PhotoRequestsTab';

export default function Dashboard({ auth, schedules, photoRequests }) {
    const user = auth.user;
    const role = user?.role || 'participant';
    const [activeTab, setActiveTab] = useState('overview');

    if (!user) {
        return null; // or loading spinner, though auth middleware prevents this
    }

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    {__('Dashboard')} {role.charAt(0).toUpperCase() + role.slice(1)}
                </h2>
            }
        >
            <Head title={__('Dashboard')} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            {/* Profile Photo Section */}
                            <div className="mb-8 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div className="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                                    <div className="relative">
                                        {user.profile_photo_url ? (
                                            <img 
                                                src={user.profile_photo_url} 
                                                alt={user.name} 
                                                className="w-24 h-24 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md"
                                            />
                                        ) : (
                                            <div className="w-24 h-24 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-400 text-2xl font-bold border-4 border-white dark:border-gray-800 shadow-md">
                                                {user.name.charAt(0)}
                                            </div>
                                        )}
                                        <div className={`absolute -bottom-1 -right-1 w-6 h-6 rounded-full border-2 border-white dark:border-gray-800 shadow-sm ${
                                            user.profile_photo_status === 'active' ? 'bg-green-500' :
                                            user.profile_photo_status === 'pending' ? 'bg-yellow-500' : 'bg-red-500'
                                        }`} title={__(`Photo Status: ${user.profile_photo_status}`)}></div>
                                    </div>
                                    
                                    <div className="flex-1 text-center md:text-left">
                                        <h4 className="text-lg font-semibold">{user.name}</h4>
                                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">{user.email}</p>
                                        <div className="flex flex-wrap justify-center md:justify-start gap-2">
                                            <span className={`px-2 py-0.5 text-xs rounded-full ${
                                                user.profile_photo_status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                                user.profile_photo_status === 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                                'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'
                                            }`}>
                                                {__('Photo Status')}: <span className="capitalize">{__(user.profile_photo_status)}</span>
                                            </span>
                                            <span className="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 capitalize">
                                                {__(user.role)}
                                            </span>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            {user.role === 'admin' && (
                                <div>
                                    {/* Tabs Navigation */}
                                    <div className="border-b border-gray-200 dark:border-gray-700 mb-6">
                                        <nav className="-mb-px flex space-x-8 overflow-x-auto">
                                            <button
                                                onClick={() => setActiveTab('overview')}
                                                className={`${
                                                    activeTab === 'overview'
                                                        ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'
                                                } whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm`}
                                            >
                                                {__('Overview')}
                                            </button>
                                            <button
                                                onClick={() => setActiveTab('schedule')}
                                                className={`${
                                                    activeTab === 'schedule'
                                                        ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'
                                                } whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm`}
                                            >
                                                {__('Schedule')}
                                            </button>
                                            <button
                                                onClick={() => setActiveTab('photos')}
                                                className={`${
                                                    activeTab === 'photos'
                                                        ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400'
                                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'
                                                } whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center`}
                                            >
                                                {__('Photo Requests')}
                                                {photoRequests && photoRequests.length > 0 && (
                                                    <span className="ml-2 bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-200 py-0.5 px-2 rounded-full text-xs">
                                                        {photoRequests.length}
                                                    </span>
                                                )}
                                            </button>
                                        </nav>
                                    </div>

                                    {/* Tab Content */}
                                    {activeTab === 'overview' && (
                                        <div className="animate-fade-in">
                                            <h3 className="text-lg font-bold mb-4">{__('Admin Panel')}</h3>
                                            <p className="mb-6">{__('Welcome, Administrator. You have full access to the system.')}</p>
                                            
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <Link
                                                    href={route('mentors.index')}
                                                    className="p-6 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition shadow-sm dark:bg-blue-900/20 dark:border-blue-800 dark:hover:bg-blue-900/30"
                                                >
                                                    <h4 className="font-semibold text-blue-700 dark:text-blue-400">{__('Mentor List')}</h4>
                                                    <p className="text-sm text-blue-600 mt-2 dark:text-blue-500">{__('Manage and view all registered mentors.')}</p>
                                                </Link>

                                                <Link
                                                    href={route('participants.index')}
                                                    className="p-6 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition shadow-sm dark:bg-green-900/20 dark:border-green-800 dark:hover:bg-green-900/30"
                                                >
                                                    <h4 className="font-semibold text-green-700 dark:text-green-400">{__('Participant List')}</h4>
                                                    <p className="text-sm text-green-600 mt-2 dark:text-green-500">{__('Manage and view all registered participants.')}</p>
                                                </Link>
                                            </div>
                                        </div>
                                    )}

                                    {activeTab === 'schedule' && (
                                        <div className="animate-fade-in">
                                            <ScheduleTab />
                                        </div>
                                    )}

                                    {activeTab === 'photos' && (
                                        <div className="animate-fade-in">
                                            <PhotoRequestsTab initialRequests={photoRequests} />
                                        </div>
                                    )}
                                </div>
                            )}
                            {role === 'mentor' && (
                                <div>
                                    <h3 className="text-lg font-bold mb-4">{__('Mentor/Implementer Panel')}</h3>
                                    <p className="mb-6">{__('Welcome, Mentor. You can manage participants and programs.')}</p>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                                        <Link
                                            href={route('participants.index')}
                                            className="p-6 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition shadow-sm dark:bg-green-900/20 dark:border-green-800 dark:hover:bg-green-900/30"
                                        >
                                            <h4 className="font-semibold text-green-700 dark:text-green-400">{__('Participant List')}</h4>
                                            <p className="text-sm text-green-600 mt-2 dark:text-green-500">{__('Manage and view all registered participants.')}</p>
                                        </Link>
                                    </div>

                                    <div className="mt-8">
                                        <h4 className="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">{__('Schedule')}</h4>
                                        <MentorScheduleTable />
                                    </div>
                                </div>
                            )}
                            {role === 'participant' && (
                                <div>
                                    <h3 className="text-lg font-bold mb-4">{__('Participant Panel')}</h3>
                                    <p>{__('Welcome to the Child Development Program. Please check your schedule.', { nickname: user.nickname || user.first_name })}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
