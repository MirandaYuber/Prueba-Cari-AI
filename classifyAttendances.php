<?php

function classifyAttendances($concepts, $attendanceIn, $attendanceOut): bool|string 
{
    $results = [];
    
    $attendanceIn = new DateTime($attendanceIn);
    $attendanceOut = new DateTime($attendanceOut);

    if ($attendanceOut < $attendanceIn) {
        $attendanceOut->modify('+1 day');
    }

    foreach ($concepts as $concept) {
        $conceptStart = new DateTime($concept['start']);
        $conceptEnd = new DateTime($concept['end']);
        
        if ($conceptEnd < $conceptStart) {
            $conceptEnd->modify('+1 day');
        }

        $startMax = max($attendanceIn, $conceptStart);
        $endMin = min($attendanceOut, $conceptEnd);

        if ($startMax < $endMin) {
            $interval = $startMax->diff($endMin);
            $hours = $interval->h + ($interval->i / 60) + ($interval->s / 3600);
            
            if ($hours > 0) {
                $results[$concept['id']] = round($hours, 1);
            }
        }
    }

    return json_encode($results);
}

$concepts = [
    ["id" => "HO", "name" => "HO", "start" => "08:00", "end" => "17:59"],
    ["id" => "HED", "name" => "HED", "start" => "18:00", "end" => "20:59"],
    ["id" => "HEN", "name" => "HEN", "start" => "21:00", "end" => "05:59"]
];

echo "Prueba 1 (08:00 - 11:30):\n";
echo classifyAttendances($concepts, "08:00", "11:30") . "\n\n"; 

echo "Prueba 2 (14:00 - 21:30):\n";
echo classifyAttendances($concepts, "14:00", "21:30") . "\n"; 