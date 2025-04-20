<?php

if (!function_exists('generateFriendlyPassword')) {
    function generateFriendlyPassword($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }
}
