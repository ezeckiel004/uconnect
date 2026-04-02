<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Stripe Connect onboarding callbacks (called by Stripe in the embedded WebView)
Route::get('/onboarding/success', function () {
    return response(
        '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Inscription réussie</title>'
        . '<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#f0f9f4;padding:24px;}'
        . '.card{background:#fff;border-radius:20px;padding:36px 28px;text-align:center;max-width:380px;box-shadow:0 4px 24px rgba(0,0,0,.08);}'
        . '.icon{font-size:56px;margin-bottom:16px;}.title{font-size:22px;font-weight:700;color:#1a2332;margin-bottom:10px;}'
        . '.body{font-size:15px;color:#6a7078;line-height:1.55;}</style>'
        . '</head><body data-status="onboarding_success">'
        . '<div class="card"><div class="icon">✅</div>'
        . '<p class="title">Inscription enregistrée&nbsp;!</p>'
        . '<p class="body">Votre compte est en attente de validation par l\'administrateur. Vous recevrez vos identifiants par email.</p>'
        . '</div></body></html>',
        200,
        ['Content-Type' => 'text/html']
    );
})->name('onboarding.success');

Route::get('/onboarding/refresh', function () {
    return response(
        '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Lien expiré</title>'
        . '<style>*{margin:0;padding:0;box-sizing:border-box;}body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#fff8f0;padding:24px;}'
        . '.card{background:#fff;border-radius:20px;padding:36px 28px;text-align:center;max-width:380px;box-shadow:0 4px 24px rgba(0,0,0,.08);}'
        . '.icon{font-size:56px;margin-bottom:16px;}.title{font-size:22px;font-weight:700;color:#1a2332;margin-bottom:10px;}'
        . '.body{font-size:15px;color:#6a7078;line-height:1.55;}</style>'
        . '</head><body data-status="onboarding_refresh">'
        . '<div class="card"><div class="icon">⏳</div>'
        . '<p class="title">Lien expiré</p>'
        . '<p class="body">Ce lien Stripe Connect n\'est plus valide. Veuillez relancer l\'inscription pour obtenir un nouveau lien.</p>'
        . '</div></body></html>',
        200,
        ['Content-Type' => 'text/html']
    );
})->name('onboarding.refresh');
