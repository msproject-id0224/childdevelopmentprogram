import { render, screen } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import PhotoRequestsTab from '../PhotoRequestsTab';

// Mock Inertia
vi.mock('@inertiajs/react', () => ({
    useForm: () => ({
        data: {},
        setData: vi.fn(),
        post: vi.fn(),
        processing: false,
        errors: {},
        reset: vi.fn(),
    }),
    router: {
        post: vi.fn(),
    },
}));

vi.mock('@/Utils/lang', () => ({
    __: (key) => key,
}));

vi.mock('@/Components/Modal', () => ({
    default: ({ show, children }) => show ? <div>{children}</div> : null,
}));

describe('PhotoRequestsTab', () => {
    it('renders safely when user name is missing', () => {
        const requests = [
            {
                id: 1,
                user: {
                    id: 1,
                    email: 'test@example.com',
                    // name is missing
                    first_name: 'John',
                },
                photo_url: 'http://example.com/photo.jpg',
            }
        ];

        render(<PhotoRequestsTab initialRequests={requests} />);
        expect(screen.getByText('John')).toBeInTheDocument();
        expect(screen.getByText('test@example.com')).toBeInTheDocument();
    });

    it('renders safely when user name and first_name are missing', () => {
        const requests = [
            {
                id: 1,
                user: {
                    id: 1,
                    email: 'test@example.com',
                },
                photo_url: 'http://example.com/photo.jpg',
            }
        ];

        render(<PhotoRequestsTab initialRequests={requests} />);
        expect(screen.getByText('User')).toBeInTheDocument();
        expect(screen.getByText('test@example.com')).toBeInTheDocument();
    });

    it('renders safely when user object is missing', () => {
        const requests = [
            {
                id: 1,
                user: null, // User deleted
                photo_url: 'http://example.com/photo.jpg',
            }
        ];

        render(<PhotoRequestsTab initialRequests={requests} />);
        // Should not crash, and show fallback or empty
        // Based on my code: request.user?.name -> undefined.
        // It might render "User" or empty string depending on logic.
        // Logic: request.user?.name || request.user?.first_name || 'User'
        // undefined || undefined || 'User' -> 'User'
        expect(screen.getByText('User')).toBeInTheDocument();
    });

    it('renders correctly when name is present', () => {
        const requests = [
            {
                id: 1,
                user: {
                    id: 1,
                    email: 'test@example.com',
                    name: 'Test User',
                },
                photo_url: 'http://example.com/photo.jpg',
            }
        ];

        render(<PhotoRequestsTab initialRequests={requests} />);
        expect(screen.getByText('Test User')).toBeInTheDocument();
    });
});
