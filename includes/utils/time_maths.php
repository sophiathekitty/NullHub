<?php
/**
 * converts seconds into a time string
 * @param int|float $seconds length of time in seconds
 * @return string h:mm:ss
 */
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
/**
 * converts seconds into a time string
 * @param int|float $seconds length of time in seconds
 * @return string h:mm
 */
function SecondsToTimeShort($seconds){
    $hours = floor($seconds/60/60);
    $seconds = $seconds - ($hours * 60 * 60);
    $minutes = floor($seconds/60);
    if($minutes < 10){
        $minutes = "0$minutes";
    }
    return "$hours:$minutes";
}
/**
 * converts seconds to minutes (ie: 60 seconds is 1 minute)
 * @param int|float $seconds
 * @return float minutes
 */
function SecondsToMinutes($seconds){
    return ($seconds/60);
}
/**
 * converts seconds to hours (ie: 3,600 seconds is 1 hour)
 * @param int|float $seconds
 * @return float hours
 */
function SecondsToHours($seconds){
    return ($seconds/60/60);
}
/**
 * converts seconds to days (ie: 86,400 seconds is 1 day)
 * @param int|float $seconds
 * @return float days
 */
function SecondsToDays($seconds){
    return ($seconds/60/60/24);
}
/**
 * converts seconds to weeks (ie: 604,800 seconds is 1 week)
 * @param int|float $seconds
 * @return float weeks
 */
function SecondsToWeeks($seconds){
    return ($seconds/60/60/24/7);
}
/**
 * converts minutes to seconds (ie: 1 minute is 60 seconds)
 * @param int|float $minutes
 * @return float seconds
 */
function MinutesToSeconds($minutes){
    return $minutes*60;
}
/**
 * converts hours to seconds (ie: 1 hour is 3,600 seconds)
 * @param int|float $hours
 * @return float seconds
 */
function HoursToSeconds($hours){
    return $hours*60*60;
}
/**
 * converts days to seconds (ie: 1 day is 86,400 seconds)
 * @param int|float $days
 * @return float seconds
 */
function DaysToSeconds($days){
    return $days*60*60*24;
}
/**
 * converts weeks to seconds (ie: 1 week is 604,800 seconds)
 * @param int|float $weeks
 * @return float seconds
 */
function WeeksToSeconds($weeks){
    return $weeks*60*60*24*7;
}

/**
 * converts minutes to hours (ie: 60 minutes is 1 hour)
 * @param int|float $minutes
 * @return float hours
 */
function MinutesToHours($minutes){
    return SecondsToHours(MinutesToSeconds($minutes));
}
/**
 * converts minutes to days (ie: 1,440 minutes is 1 day)
 * @param int|float $minutes
 * @return float days
 */
function MinutesToDays($minutes){
    return SecondsToDays(MinutesToSeconds($minutes));
}
/**
 * converts minutes to weeks (ie: 10,080 minutes is 1 day)
 * @param int|float $minutes
 * @return float weeks
 */
function MinutesToWeeks($minutes){
    return SecondsToWeeks(MinutesToSeconds($minutes));
}

/**
 * converts hours to minutes (ie: 1 hour is 60 minutes)
 * @param int|float $hours
 * @return float minutes
 */
function HoursToMinutes($hours){
    return SecondsToMinutes(HoursToSeconds($hours));
}
/**
 * converts hours to days (ie: 24 hours is 1 day)
 * @param int|float $hours
 * @return float days
 */
function HoursToDays($hours){
    return SecondsToDays(HoursToSeconds($hours));
}
/**
 * converts hours to weeks (ie: 168 hours is 1 week)
 * @param int|float $hours
 * @return float weeks
 */
function HoursToWeeks($hours){
    return SecondsToWeeks(HoursToSeconds($hours));
}

/**
 * converts days to minutes (ie: 1 day is 1,440 minutes)
 * @param int|float $days
 * @return float minutes
 */
function DaysToMinutes($days){
    return SecondsToMinutes(DaysToSeconds($days));
}
/**
 * converts days to hours (ie: 1 day is 24 hours)
 * @param int|float $days
 * @return float hours
 */
function DaysToHours($days){
    return SecondsToHours(DaysToSeconds($days));
}
/**
 * converts days to minutes (ie: 7 day is 1 week)
 * @param int|float $days
 * @return float weeks
 */
function DaysToWeeks($days){
    return SecondsToWeeks(DaysToSeconds($days));
}

/**
 * converts weeks to days (ie: 1 week is 7 days)
 * @param int|float $weeks
 * @return float days
 */
function WeeksToDays($weeks){
    return SecondsToDays(WeeksToSeconds($weeks));
}
/**
 * converts weeks to hours (ie: 1 week is 168 hours)
 * @param int|float $weeks
 * @return float hours
 */
function WeeksToHours($weeks){
    return SecondsToHours(WeeksToSeconds($weeks));
}
/**
 * converts weeks to minutes (ie: 1 week is  10,080 minutes)
 * @param int|float $weeks
 * @return float minutes
 */
function WeeksToMinutes($weeks){
    return SecondsToMinutes(WeeksToSeconds($weeks));
}

?>