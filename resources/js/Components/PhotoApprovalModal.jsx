import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import DangerButton from '@/Components/DangerButton';
import SecondaryButton from '@/Components/SecondaryButton';
import InputLabel from '@/Components/InputLabel';
import InputError from '@/Components/InputError';
import { __ } from '@/Utils/lang';
import { useState } from 'react';

export default function PhotoApprovalModal({ 
    show, 
    onClose, 
    request, 
    onApprove, 
    onReject, 
    processing,
    errors 
}) {
    const [reason, setReason] = useState('');
    const [isRejecting, setIsRejecting] = useState(false);

    if (!request) return null;

    const handleApprove = () => {
        onApprove(request.id);
    };

    const handleReject = (e) => {
        e.preventDefault();
        onReject(request.id, reason);
    };

    const resetAndClose = () => {
        setIsRejecting(false);
        setReason('');
        onClose();
    };

    return (
        <Modal show={show} onClose={resetAndClose} maxWidth="2xl">
            <div className="p-6">
                <div className="flex justify-between items-start mb-6">
                    <h2 className="text-xl font-bold text-gray-900 dark:text-gray-100">
                        {__('Profile Photo Approval')}
                    </h2>
                    <button 
                        onClick={resetAndClose}
                        className="text-gray-400 hover:text-gray-500 focus:outline-none"
                    >
                        <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* Preview Section */}
                    <div className="flex flex-col items-center">
                        <div className="relative group w-full aspect-square overflow-hidden rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                            <img 
                                src={`/storage/${request.photo_path}`} 
                                alt={request.user.name} 
                                className="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
                            />
                            <div className="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300" />
                        </div>
                        <p className="mt-2 text-xs text-gray-500 italic">
                            {__('Click to zoom (coming soon)')}
                        </p>
                    </div>

                    {/* Details Section */}
                    <div className="flex flex-col space-y-4">
                        <div className="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 space-y-3">
                            <div>
                                <h3 className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {__('Uploader')}
                                </h3>
                                <p className="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {request.user.name}
                                </p>
                                <p className="text-xs text-gray-500">
                                    {request.user.email}
                                </p>
                                <span className="inline-flex mt-1 items-center px-2 py-0.5 rounded text-[10px] font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400 capitalize">
                                    {__(request.user.role)}
                                </span>
                            </div>

                            <div>
                                <h3 className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {__('Upload Date')}
                                </h3>
                                <p className="text-sm text-gray-900 dark:text-gray-100">
                                    {new Date(request.created_at).toLocaleString()}
                                </p>
                            </div>

                            {request.description && (
                                <div>
                                    <h3 className="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {__('Description')}
                                    </h3>
                                    <p className="text-sm text-gray-900 dark:text-gray-100">
                                        {request.description}
                                    </p>
                                </div>
                            )}
                        </div>

                        {!isRejecting ? (
                            <div className="flex flex-col space-y-3 pt-2">
                                <PrimaryButton 
                                    onClick={handleApprove}
                                    className="w-full justify-center bg-green-600 hover:bg-green-700 active:bg-green-800 focus:ring-green-500"
                                    disabled={processing}
                                >
                                    {processing ? __('Processing...') : __('Approve')}
                                </PrimaryButton>
                                <DangerButton 
                                    onClick={() => setIsRejecting(true)}
                                    className="w-full justify-center"
                                    disabled={processing}
                                >
                                    {__('Reject')}
                                </DangerButton>
                            </div>
                        ) : (
                            <form onSubmit={handleReject} className="flex flex-col space-y-3 pt-2 animate-in fade-in slide-in-from-top-4 duration-300">
                                <div>
                                    <InputLabel htmlFor="rejection_reason" value={__('Rejection Reason (Optional)')} />
                                    <textarea
                                        id="rejection_reason"
                                        className="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm"
                                        rows="3"
                                        value={reason}
                                        onChange={(e) => setReason(e.target.value)}
                                        placeholder={__('Explain why this photo is being rejected...')}
                                        autoFocus
                                    ></textarea>
                                    <InputError message={errors.reason} className="mt-1" />
                                </div>
                                <div className="flex space-x-2">
                                    <SecondaryButton 
                                        onClick={() => setIsRejecting(false)}
                                        className="flex-1 justify-center"
                                        disabled={processing}
                                    >
                                        {__('Back')}
                                    </SecondaryButton>
                                    <DangerButton 
                                        type="submit"
                                        className="flex-1 justify-center"
                                        disabled={processing}
                                    >
                                        {processing ? __('Rejecting...') : __('Confirm Reject')}
                                    </DangerButton>
                                </div>
                            </form>
                        )}
                    </div>
                </div>
            </div>
        </Modal>
    );
}
