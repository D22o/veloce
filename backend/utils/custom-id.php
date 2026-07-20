<?php

function isValidDateFormat(string $date, string $format = 'Y-m-d') 
{
    $d = \DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}