<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RmdChartService
{
    /**
     * Get age distribution data for all participants.
     * Categories: 12-14, 15-18, 19+
     *
     * @return array
     */
    public function getAgeDistributionAllParticipants()
    {
        // Only consider participants >= 12 years old as per general requirement
        $dobs = User::where('role', 'participant')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '<=', now()->subYears(12)->toDateString())
            ->pluck('date_of_birth');
            
        $ranges = [
            '12-14 Tahun' => 0,
            '15-18 Tahun' => 0,
            '≥ 19 Tahun' => 0,
        ];

        // Total for calculation should be based on filtered result
        $total = $dobs->count();

        foreach ($dobs as $dob) {
            $age = \Carbon\Carbon::parse($dob)->age;
            
            if ($age >= 12 && $age <= 14) $ranges['12-14 Tahun']++;
            elseif ($age >= 15 && $age <= 18) $ranges['15-18 Tahun']++;
            else $ranges['≥ 19 Tahun']++;
        }

        // Add percentages to labels
        $rangesWithPercentage = [];
        foreach ($ranges as $key => $value) {
            $percentage = $total > 0 ? round(($value / $total) * 100, 1) : 0;
            $newKey = "{$key} ({$percentage}%)";
            $rangesWithPercentage[$newKey] = $value;
        }

        return $this->formatChartData($rangesWithPercentage, 'Distribusi Usia');
    }

    /**
     * Get participation rate (Filled RMD vs Not Filled) for participants >= 12 years old.
     *
     * @return array
     */
    public function getRmdParticipationRate()
    {
        // Filter participants by age >= 12
        $totalParticipants = User::where('role', 'participant')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '<=', now()->subYears(12)->toDateString())
            ->count();
        
        // Get user IDs who have filled at least one RMD module
        $modules = RmdProgressService::getModules();
        $queries = [];
        
        foreach ($modules as $moduleName => $modelClass) {
            $model = new $modelClass;
            $tableName = $model->getTable();
            if (Schema::hasColumn($tableName, 'user_id')) {
                $queries[] = DB::table($tableName)->select('user_id');
            }
        }

        if (empty($queries)) {
            $filledCount = 0;
        } else {
            $unionQuery = $queries[0];
            for ($i = 1; $i < count($queries); $i++) {
                $unionQuery->union($queries[$i]);
            }
            
            // Count unique user_id in the union result that are also participants AND >= 12 years old
            $filledCount = DB::table('users')
                ->joinSub($unionQuery, 'active_rmd_users', function ($join) {
                    $join->on('users.id', '=', 'active_rmd_users.user_id');
                })
                ->where('role', 'participant')
                ->whereNotNull('date_of_birth')
                ->where('date_of_birth', '<=', now()->subYears(12)->toDateString())
                ->count();
        }

        $notFilledCount = max(0, $totalParticipants - $filledCount);

        return [
            'labels' => ['Sudah Mengisi', 'Belum Mengisi'],
            'datasets' => [
                [
                    'label' => 'Partisipasi RMD',
                    'data' => [$filledCount, $notFilledCount],
                    'backgroundColor' => ['#10b981', '#ef4444'], // Green, Red
                ]
            ],
            'total' => $totalParticipants
        ];
    }

    /**
     * Get distribution of filled modules count per participant (12+ years old).
     * Intervals: 0, 1-3, 4-6, 7-9 (Completed)
     *
     * @return array
     */
    public function getRmdFillingProgressDistribution()
    {
        $modules = RmdProgressService::getModules();
        $totalModules = count($modules); // Should be 9
        
        // We need to count how many modules each participant has filled.
        // This is complex. We can reuse the logic from getParticipantSummaryData but optimized.
        // Or we can count (user_id) from each table and sum them up per user.
        
        $selects = [];
        foreach ($modules as $moduleName => $modelClass) {
            $model = new $modelClass;
            $tableName = $model->getTable();
            if (Schema::hasColumn($tableName, 'user_id')) {
                // We just want to know if a user exists in this table
                $selects[] = "SELECT user_id, 1 as filled_count FROM {$tableName}";
            }
        }
        
        if (empty($selects)) {
             return $this->formatChartData([]);
        }

        $unionSql = implode(' UNION ALL ', $selects);
        
        // Count filled modules per user
        $filledCounts = DB::table(DB::raw("({$unionSql}) as all_fills"))
            ->select('user_id', DB::raw('count(*) as total_filled'))
            ->groupBy('user_id')
            ->pluck('total_filled', 'user_id');
            
        // Get all participants >= 12 years old to include those with 0 filled
        $participantIds = User::where('role', 'participant')
            ->whereNotNull('date_of_birth')
            ->where('date_of_birth', '<=', now()->subYears(12)->toDateString())
            ->pluck('id');
        
        $ranges = [
            '0 Modul' => 0,
            '1-3 Modul' => 0,
            '4-6 Modul' => 0,
            '7-9 Modul' => 0, // Assuming 9 is max
        ];
        
        // If max modules > 9, adjust label
        if ($totalModules > 9) {
             $ranges['> 9 Modul'] = 0;
        }

        foreach ($participantIds as $id) {
            $count = $filledCounts->get($id, 0);
            
            if ($count == 0) $ranges['0 Modul']++;
            elseif ($count >= 1 && $count <= 3) $ranges['1-3 Modul']++;
            elseif ($count >= 4 && $count <= 6) $ranges['4-6 Modul']++;
            elseif ($count >= 7 && $count <= 9) $ranges['7-9 Modul']++;
            else {
                if (isset($ranges['> 9 Modul'])) $ranges['> 9 Modul']++;
                else $ranges['7-9 Modul']++; // Fallback
            }
        }

        return $this->formatChartData($ranges, 'Progress Pengisian');
    }

    /**
     * Format the data for Chart.js and include total count.
     *
     * @param array $ranges
     * @param string $label
     * @return array
     */
    private function formatChartData(array $ranges, $label = 'Jumlah Partisipan')
    {
        $dataValues = !empty($ranges) ? array_values($ranges) : [];
        $totalParticipants = array_sum($dataValues);

        return [
            'labels' => !empty($ranges) ? array_keys($ranges) : [],
            'datasets' => [
                [
                    'label' => $label,
                    'data' => $dataValues,
                    'backgroundColor' => ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316'],
                ]
            ],
            'total' => $totalParticipants
        ];
    }
}
