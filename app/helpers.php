<?php

if (!function_exists('maskEmail')) {
    function maskEmail($email)
    {
        if (!$email)
            return '';
        $parts = explode('@', $email);
        if (count($parts) !== 2)
            return $email;

        $localPart = $parts[0];
        $domain = $parts[1];

        if (strlen($localPart) <= 2) {
            return str_repeat('*', strlen($localPart)) . '@' . $domain;
        }

        return $localPart[0] . str_repeat('*', strlen($localPart) - 2) . $localPart[strlen($localPart) - 1] . '@' . $domain;
    }
}

if (!function_exists('maskPhone')) {
    function maskPhone($phone)
    {
        if (!$phone)
            return '';
        $visibleDigits = 4;
        $phoneLength = strlen($phone);

        if ($phoneLength <= $visibleDigits) {
            return str_repeat('*', $phoneLength);
        }

        $maskedPart = str_repeat('*', $phoneLength - $visibleDigits);
        $visiblePart = substr($phone, -$visibleDigits);

        return $maskedPart . $visiblePart;
    }
}
