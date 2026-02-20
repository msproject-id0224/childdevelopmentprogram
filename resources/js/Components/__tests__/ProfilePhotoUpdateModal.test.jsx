import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import ProfilePhotoUpdateModal from '../ProfilePhotoUpdateModal';

// Mock dependencies
vi.mock('@/Components/Modal', () => ({ default: ({ children, show }) => show ? <div data-testid="modal">{children}</div> : null }));
vi.mock('@/Components/PrimaryButton', () => ({ default: ({ children, onClick }) => <button onClick={onClick}>{children}</button> }));
vi.mock('@/Components/SecondaryButton', () => ({ default: ({ children, onClick }) => <button onClick={onClick}>{children}</button> }));
vi.mock('@/Utils/lang', () => ({ __: (key) => key }));

// Mock Inertia usePage and useForm
const mockUsePage = vi.fn();
const mockUseForm = vi.fn();

vi.mock('@inertiajs/react', () => ({
    usePage: () => mockUsePage(),
    useForm: (initialData) => mockUseForm(initialData),
}));

describe('ProfilePhotoUpdateModal Component', () => {
    it('renders correctly with valid user name', () => {
        mockUsePage.mockReturnValue({
            props: {
                auth: {
                    user: {
                        name: 'Test User',
                        profile_photo_url: null,
                        profile_photo_status: 'active'
                    }
                }
            }
        });

        mockUseForm.mockReturnValue({
            data: {},
            setData: vi.fn(),
            post: vi.fn(),
            processing: false,
            errors: {},
            reset: vi.fn(),
            clearErrors: vi.fn()
        });

        render(<ProfilePhotoUpdateModal show={true} onClose={() => {}} />);
        
        expect(screen.getByText('T')).toBeInTheDocument(); // First char of 'Test User'
    });

    it('renders correctly with missing name (uses fallback)', () => {
        mockUsePage.mockReturnValue({
            props: {
                auth: {
                    user: {
                        name: null,
                        first_name: null,
                        profile_photo_url: null,
                        profile_photo_status: 'active'
                    }
                }
            }
        });

        mockUseForm.mockReturnValue({
            data: {},
            setData: vi.fn(),
            post: vi.fn(),
            processing: false,
            errors: {},
            reset: vi.fn(),
            clearErrors: vi.fn()
        });

        render(<ProfilePhotoUpdateModal show={true} onClose={() => {}} />);
        
        expect(screen.getByText('U')).toBeInTheDocument(); // Default fallback 'U'
    });
});
