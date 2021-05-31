<?php
function SecondsToTimeFull($seconds){
    $hours = floor($seconds/60/60);
    $seconds = $seconds - ($hours * 60 * 60);
    $minutes = floor($seconds/60);
    $seconds = $seconds - ($minutes * 60);
    if($minutes < 10){
        $minutes = "0$minutes";
    }
    if($seconds < 10){
        $seconds = "0$seconds";
    }
    return "$hours:$minutes:$seconds";
}
function SecondsToTimeShort($seconds){
    $hours = floor($seconds/60/60);
    $seconds = $seconds - ($hours * 60 * 60);
    $minutes = floor($seconds/60);
    if($minutes < 10){
        $minutes = "0$minutes";
    }
    return "$hours:$minutes";
}
// seconds, minutes, hours, days
function SecondsToMinutes($seconds){
    return ($seconds/60);
}
function SecondsToHours($seconds){
    return ($seconds/60/60);
}
function SecondsToDays($seconds){
    return ($seconds/60/60/24);
}
function SecondsToWeeks($seconds){
    return ($seconds/60/60/24/7);
}
function MinutesToSeconds($minutes){
    return $minutes*60;
}
function HoursToSeconds($hours){
    return $hours*60*60;
}
function DaysToSeconds($days){
    return $days*60*60*24;
}
function WeeksToSeconds($days){
    return $days*60*60*24*7;
}

function MinutesToHours($minutes){
    return SecondsToHours(MinutesToSeconds($minutes));
}
function MinutesToDays($minutes){
    return SecondsToDays(MinutesToSeconds($minutes));
}
function MinutesToWeeks($minutes){
    return SecondsToWeeks(MinutesToSeconds($minutes));
}

function HoursToMinutes($hours){
    return SecondsToMinutes(HoursToSeconds($hours));
}
function HoursToDays($hours){
    return SecondsToDays(HoursToSeconds($hours));
}
function HoursToWeeks($hours){
    return SecondsToWeeks(HoursToSeconds($hours));
}

function DaysToMinutes($days){
    return SecondsToMinutes(DaysToSeconds($days));
}
function DaysToHours($days){
    return SecondsToHours(DaysToSeconds($days));
}
function DaysToWeeks($days){
    return SecondsToWeeks(DaysToSeconds($days));
}

function WeeksToDays($weeks){
    return SecondsToDays(WeeksToSeconds($weeks));
}
function WeeksToHours($weeks){
    return SecondsToHours(WeeksToSeconds($weeks));
}
function WeeksToMinutes($weeks){
    return SecondsToMinutes(WeeksToSeconds($weeks));
}

?>