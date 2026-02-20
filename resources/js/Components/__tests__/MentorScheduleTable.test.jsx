import { render, screen, waitFor } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import MentorScheduleTable from '../MentorScheduleTable';

// Mock dependencies
vi.mock('@/Components/TextInput', () => ({ default: (props) => <input {...props} data-testid="text-input" /> }));
vi.mock('@/Components/InputLabel', () => ({ default: ({ children }) => <label>{children}</label> }));
vi.mock('@/Components/Modal', () => ({ default: ({ children, show }) => show ? <div data-testid="modal">{children}</div> : null }));
vi.mock('@/Components/PrimaryButton', () => ({ default: ({ children, onClick }) => <button onClick={onClick}>{children}</button> }));
vi.mock('@/Components/SecondaryButton', () => ({ default: ({ children, onClick }) => <button onClick={onClick}>{children}</button> }));
vi.mock('@/Utils/lang', () => ({ __: (key) => key }));

// Mock axios
const mockAxios = {
    get: vi.fn(),
};
window.axios = mockAxios;

describe('MentorScheduleTable Component', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders and handles problematic data safely', async () => {
        const problematicSchedules = [
            {
                id: 1,
                name: 'Test Participant',
                date: '2023-01-01',
                start_time: '10:00:00',
                end_time: '11:00:00',
                // Missing priority, Missing status
                priority: null,
                status: null
            },
            {
                id: 2,
                name: 'Number Priority',
                date: '2023-01-02',
                start_time: '11:00:00',
                end_time: '12:00:00',
                priority: 123, // Number instead of string
                status: 'scheduled'
            }
        ];

        mockAxios.get.mockResolvedValue({
            data: {
                data: problematicSchedules,
                current_page: 1,
                last_page: 1,
                from: 1,
                to: 2,
                total: 2,
                links: []
            }
        });

        render(<MentorScheduleTable />);

        // Should be loading initially
        // await waitFor(() => expect(screen.getByText('Test Participant')).toBeInTheDocument());
        
        // Check for safe rendering of null priority/status
        await waitFor(() => {
            expect(screen.getByText('Test Participant')).toBeInTheDocument();
        });

        // The table should render '-' for null priority (based on code: schedule.priority ? ... : '-')
        expect(screen.getAllByText('-').length).toBeGreaterThan(0);

        // The table should render 'Scheduled' for null status (based on code: schedule.status ? ... : 'Scheduled')
        // Wait, let's check the code again.
        // {schedule.status ? String(schedule.status).charAt(0).toUpperCase() + String(schedule.status).slice(1) : 'Scheduled'}
        // So null status becomes 'Scheduled'.
        expect(screen.getAllByText('Scheduled').length).toBeGreaterThan(0);

        // Check for number priority conversion
        // String(123).charAt(0).toUpperCase() + String(123).slice(1) => "1" + "23" => "123"
        expect(screen.getByText('123')).toBeInTheDocument();
    });
});
