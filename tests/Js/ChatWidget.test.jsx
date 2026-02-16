import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import ChatWidget from '@/Components/ChatWidget';
import { vi, describe, it, expect, beforeEach } from 'vitest';
import axios from 'axios';

// Mock Inertia usePage
vi.mock('@inertiajs/react', () => ({
    usePage: () => ({
        props: {
            translations: {},
        }
    }),
}));

// Mock axios
vi.mock('axios');

describe('ChatWidget', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        // Default mock implementation for axios
        axios.get.mockResolvedValue({ data: [] });
        axios.post.mockResolvedValue({ data: {} });
    });

    it('opens chat with specific user when start-chat event is dispatched', async () => {
        render(<ChatWidget user={{ id: 1, name: 'Test User' }} />);
        
        const targetUser = { id: 2, name: 'Target Mentor' };
        
        // Dispatch custom event wrapped in act
        await act(async () => {
            const event = new CustomEvent('start-chat', {
                detail: { user: targetUser }
            });
            window.dispatchEvent(event);
        });
        
        // Check if chat window is open and target name is displayed
        await waitFor(() => {
            expect(screen.getByText('Target Mentor')).toBeInTheDocument();
        });
        
        // Check back button exists
        expect(screen.getByTitle('Back to Support')).toBeInTheDocument();
    });

    it('toggles chat window', async () => {
        render(<ChatWidget user={{ id: 1, name: 'Test User' }} />);
        
        const toggleButton = screen.getByLabelText('Open Chat');
        fireEvent.click(toggleButton);
        
        expect(screen.getByText('Live Chat')).toBeInTheDocument();

        // Wait for the fetch in useEffect to complete
        await waitFor(() => {
            expect(axios.get).toHaveBeenCalled();
        });
    });

    it('switches to users tab and displays users', async () => {
        const mockUsers = [
            { id: 2, name: 'Mentor Alice', role: 'mentor', status: 'online', avatar: 'avatar.jpg', last_active: '2 min ago' },
            { id: 3, name: 'User Bob', role: 'participant', status: 'online', avatar: 'avatar.jpg', last_active: '5 min ago' }
        ];

        axios.get.mockImplementation((url) => {
            if (url === '/api/online-users') {
                return Promise.resolve({ data: mockUsers });
            }
            return Promise.resolve({ data: [] });
        });

        render(<ChatWidget user={{ id: 1, name: 'Test User' }} />);
        
        // Open chat
        fireEvent.click(screen.getByLabelText('Open Chat'));
        
        // Click Users tab
        const usersTab = screen.getByText('Users');
        fireEvent.click(usersTab);
        
        // Check if fetch was called
        expect(axios.get).toHaveBeenCalledWith('/api/online-users', expect.any(Object));

        // Check if users are displayed
        await waitFor(() => {
            expect(screen.getByText('Mentor Alice')).toBeInTheDocument();
            expect(screen.getByText('User Bob')).toBeInTheDocument();
        });
    });

    it('searches for users', async () => {
        render(<ChatWidget user={{ id: 1, name: 'Test User' }} />);
        
        fireEvent.click(screen.getByLabelText('Open Chat'));
        fireEvent.click(screen.getByText('Users'));
        
        const searchInput = screen.getByPlaceholderText('Search users...');
        fireEvent.change(searchInput, { target: { value: 'Alice' } });
        
        await waitFor(() => {
             expect(axios.get).toHaveBeenCalledWith('/api/online-users', expect.objectContaining({
                params: { query: 'Alice' }
            }));
        });
    });
});
