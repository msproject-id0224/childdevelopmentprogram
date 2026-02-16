import { render, screen, fireEvent } from '@testing-library/react';
import MentorIndex from '@/Pages/Mentor/Index';
import { vi, describe, it, expect } from 'vitest';

// Mock dependencies
vi.mock('@inertiajs/react', () => ({
    Link: ({ children, ...props }) => <a {...props}>{children}</a>,
    Head: () => null,
    router: { patch: vi.fn() },
    usePage: () => ({ props: { auth: { user: { role: 'admin' } } } })
}));

vi.mock('@/Layouts/AuthenticatedLayout', () => ({
    default: ({ children }) => <div>{children}</div>
}));

vi.mock('@/Utils/lang', () => ({
    __: (key) => key,
}));

// Mock route
global.route = vi.fn((name, param) => `#${name}/${param || ''}`);

describe('MentorIndex Chat Feature', () => {
    const mockMentors = [
        {
            id: 1,
            first_name: 'John',
            last_name: 'Doe',
            email: 'john@example.com',
            role: 'mentor',
            is_active: true
        }
    ];

    it('renders chat button for each mentor', () => {
        render(<MentorIndex auth={{ user: { role: 'admin' } }} mentors={mockMentors} />);
        
        const chatButtons = screen.getAllByTitle('Chat');
        expect(chatButtons).toHaveLength(mockMentors.length);
    });

    it('dispatches start-chat event when chat button is clicked', () => {
        render(<MentorIndex auth={{ user: { role: 'admin' } }} mentors={mockMentors} />);
        
        // Mock window.dispatchEvent
        const dispatchEventSpy = vi.spyOn(window, 'dispatchEvent');
        
        const chatButton = screen.getByTitle('Chat');
        fireEvent.click(chatButton);
        
        expect(dispatchEventSpy).toHaveBeenCalled();
        
        const event = dispatchEventSpy.mock.calls[0][0];
        expect(event.type).toBe('start-chat');
        expect(event.detail.user).toEqual(mockMentors[0]);
    });
});
