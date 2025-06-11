<?php

function getCurrentLanguage() {
    return isset($_SESSION['language']) ? $_SESSION['language'] : 'vi';
}

function setLanguage($lang) {
    $_SESSION['language'] = $lang;
}

function __($key) {
    $languages = require __DIR__ . '/languages.php';
    $currentLang = getCurrentLanguage();
    
    return $languages[$currentLang][$key] ?? $key;
}

function getLanguageOptions() {
    return [
        'en' => [
            'name' => 'English',
            'flag' => 'gb'
        ],
        'vi' => [
            'name' => 'Tiếng Việt',
            'flag' => 'vn'
        ]
    ];
}

function formatCurrency($amount, $currency = 'VND') {
    if ($currency === 'VND') {
        // Format as VND (no decimal places, group thousands, add ₫ symbol)
        return number_format($amount, 0, ',', '.') . '₫';
    } else {
        // USD format as fallback
        return '$' . number_format($amount, 2, '.', ',');
    }
} 