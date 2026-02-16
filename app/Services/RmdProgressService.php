<?php

namespace App\Services;

use App\Models\RmdProfile;
use App\Models\RmdBibleReflection;
use App\Models\RmdTrueSuccess;
use App\Models\RmdTheOnlyOne;
use App\Models\RmdMultipleIntelligence;
use App\Models\RmdSocioEmotional;
use App\Models\RmdCareerExploration;
use App\Models\RmdCareerExplorationP2;
use App\Models\RmdPreparationDreamIsland;
use Illuminate\Database\Eloquent\Model;

class RmdProgressService
{
    public static function getModules()
    {
        return [
            'Profil RMD' => RmdProfile::class,
            'Refleksi Alkitab' => RmdBibleReflection::class,
            'Sukses Sejati' => RmdTrueSuccess::class,
            'The Only One' => RmdTheOnlyOne::class,
            'Kecerdasan Majemuk' => RmdMultipleIntelligence::class,
            'Sosial Emosional' => RmdSocioEmotional::class,
            'Eksplorasi Karir' => RmdCareerExploration::class,
            'Eksplorasi Karir P2' => RmdCareerExplorationP2::class,
            'Persiapan Pulau Impian' => RmdPreparationDreamIsland::class,
        ];
    }

    public static function calculateProgress($user, $moduleName, $modelClass)
    {
        // Check relationship method name convention
        // Assuming relationship is camelCase of module name or derived from model
        // We might need to manually map model class to relationship name on User model
        // Or just query using where('user_id', $user->id)->first()
        
        $record = $modelClass::where('user_id', $user->id)->first();

        if (!$record) {
            return [
                'status' => 'Belum Mengisi',
                'percentage' => 0,
                'last_updated' => null,
                'filled_at' => null,
            ];
        }

        // Calculate percentage
        $fillable = $record->getFillable();
        // If fillable is empty (guarded = []), we need to get columns from schema or attributes
        // For simplicity, let's use the attributes of the retrieved record, excluding timestamps and id
        $attributes = $record->getAttributes();
        $excluded = ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at'];
        
        $totalFields = 0;
        $filledFields = 0;

        foreach ($attributes as $key => $value) {
            if (in_array($key, $excluded)) continue;
            
            $totalFields++;
            if (!empty($value)) {
                $filledFields++;
            }
        }

        $percentage = $totalFields > 0 ? round(($filledFields / $totalFields) * 100) : 0;
        
        $status = 'Sedang Mengisi';
        if ($percentage == 100) {
            $status = 'Selesai Mengisi';
        } elseif ($percentage == 0) {
            $status = 'Belum Mengisi'; // Should not happen if record exists, but possible if all fields empty
        }

        return [
            'status' => $status,
            'percentage' => $percentage,
            'last_updated' => $record->updated_at,
            'filled_at' => $record->created_at, // Or updated_at if we consider last edit as filling date
        ];
    }
}
