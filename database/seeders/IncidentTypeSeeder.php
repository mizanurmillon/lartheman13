<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentType;
use App\Models\Category;

class IncidentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            '🔴 Criminal Activity' => [
                ['name' => 'Vehicle Break-In', 'share_regionally' => true],
                ['name' => 'Theft', 'share_regionally' => true],
                ['name' => 'Vandalism (Graffiti, Property Damage)', 'share_regionally' => true],
                ['name' => 'Trespassing', 'share_regionally' => true],
                ['name' => 'Suspicious Package or Item', 'share_regionally' => true],
                ['name' => 'Attempted Arson', 'share_regionally' => true],
                ['name' => 'Drug Use or Paraphernalia Found', 'share_regionally' => true],
            ],
            '🟠 Disruptive Behavior' => [
                ['name' => 'Unstable Guest (Mental Health Concern)', 'share_regionally' => false],
                ['name' => 'Verbally Aggressive Person', 'share_regionally' => false],
                ['name' => 'Physical Altercation', 'share_regionally' => true],
                ['name' => 'Intoxicated Individual', 'share_regionally' => false],
                ['name' => 'Protester or Demonstrator', 'share_regionally' => true],
                ['name' => 'Domestic Dispute on Premises', 'share_regionally' => false],
            ],
            '🟡 Suspicious or Concerning' => [
                ['name' => 'Loitering', 'share_regionally' => true],
                ['name' => 'Repeated Drive-Bys', 'share_regionally' => true],
                ['name' => 'Unknown Person Taking Photos or Notes', 'share_regionally' => true],
                ['name' => 'Drone Activity', 'share_regionally' => true],
                ['name' => 'Unfamiliar Person Asking Unusual Questions', 'share_regionally' => true],
            ],
            '🔵 Potential Threat Indicators' => [
                ['name' => 'Threatening Note or Message', 'share_regionally' => true],
                ['name' => 'Vehicle Loitering in Lot', 'share_regionally' => true],
                ['name' => 'Security Breach Attempt', 'share_regionally' => true],
                ['name' => 'Gun or Weapon Sighting Reported', 'share_regionally' => true],
            ],
            '🧯 Safety or Facility Issues' => [
                ['name' => 'Fire Alarm Activation', 'share_regionally' => false],
                ['name' => 'Medical Emergency', 'share_regionally' => false],
                ['name' => 'Slip and Fall', 'share_regionally' => false],
                ['name' => 'Missing Child / Lost Person', 'share_regionally' => false],
                ['name' => 'Power Outage / Utility Issue', 'share_regionally' => false],
                ['name' => 'Water Leak or Flood', 'share_regionally' => false],
            ],
            '📋 Policy-Related Reports' => [
                ['name' => 'Access Control Failure (e.g., door left open)', 'share_regionally' => false],
                ['name' => 'Unattended Bag', 'share_regionally' => true],
                ['name' => 'Volunteer/Staff Non-Compliance', 'share_regionally' => false],
                ['name' => 'Security Camera Malfunction', 'share_regionally' => false],
                ['name' => 'Incident During Special Event', 'share_regionally' => true],
            ],
        ];

        foreach ($categories as $catName => $incidentTypes) {
            $category = Category::firstOrCreate(['name' => $catName]);
            foreach ($incidentTypes as $type) {
                IncidentType::updateOrCreate([
                    'category_id' => $category->id,
                    'name' => $type['name']
                ], [
                    'share_regionally' => $type['share_regionally']
                ]);
            }
        }
    }
}
