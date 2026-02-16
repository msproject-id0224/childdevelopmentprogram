import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { vi, describe, it, expect } from 'vitest';
import MentorCreate from '../../resources/js/Pages/Mentor/Create';
import React from 'react';

// Mock Inertia components
vi.mock('@inertiajs/react', async () => {
    const React = await import('react');
    return {
        useForm: (initialValues) => {
            const [data, setData] = React.useState(initialValues);
            const post = vi.fn();
            return {
                data,
                setData: (key, value) => setData(prev => ({ ...prev, [key]: value })),
                post,
                errors: {},
                processing: false,
            };
        },
        Head: () => null,
        Link: ({ children, href }) => <a href={href}>{children}</a>,
    };
});

// Mock Layout
vi.mock('@/Layouts/AuthenticatedLayout', () => ({
    default: ({ children }) => <div>{children}</div>,
}));

// Mock translation
vi.mock('@/Utils/lang', () => ({
    __: (key) => key,
}));

// Mock route
global.route = vi.fn((name) => name);

// Mock Modal
vi.mock('@/Components/Modal', () => ({
    default: ({ show, children }) => show ? <div data-testid="modal">{children}</div> : null,
}));

describe('MentorCreate', () => {
    it('shows confirmation modal when form is submitted with valid data', async () => {
        render(<MentorCreate />);

        // Fill required fields
        fireEvent.change(screen.getByLabelText('First Name'), { target: { value: 'John' } });
        fireEvent.change(screen.getByLabelText('Last Name'), { target: { value: 'Doe' } });
        fireEvent.change(screen.getByLabelText('Email'), { target: { value: 'john@example.com' } });
        fireEvent.change(screen.getByLabelText('Role'), { target: { value: 'mentor' } });
        
        // Submit form
        fireEvent.click(screen.getByText('Create'));

        // Check if modal is shown
        expect(screen.getByTestId('modal')).toBeInTheDocument();
        expect(screen.getByText('Confirm Mentor Creation')).toBeInTheDocument();
        expect(screen.getByText('John Doe')).toBeInTheDocument();
        expect(screen.getByText('john@example.com')).toBeInTheDocument();
    });

    it('does not show modal if required fields are missing', () => {
        render(<MentorCreate />);
        
        // Don't fill anything
        fireEvent.click(screen.getByText('Create'));
        
        // Modal should NOT be shown
        expect(screen.queryByTestId('modal')).not.toBeInTheDocument();
    });
});
