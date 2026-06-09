<?php

use App\Services\CategoryClassifierService;

test('it classifies makan ketoprak as Needs - Makan & Minum', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Saya membeli ketoprak seharga 15000');
    
    expect($result)->toBe([
        'group' => 'Needs',
        'sub_category' => 'Makan & Minum',
    ]);
});

test('it classifies grab/ojol as Needs - Transportasi & Kendaraan', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Bayar ojol tadi pagi 25000');
    
    expect($result)->toBe([
        'group' => 'Needs',
        'sub_category' => 'Transportasi & Kendaraan',
    ]);
});

test('it classifies starbucks as Wants - Nongkrong & Hiburan', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Beli kopi starbucks siang ini 45000');
    
    expect($result)->toBe([
        'group' => 'Wants',
        'sub_category' => 'Nongkrong & Hiburan',
    ]);
});

test('it classifies shopee top up as Wants - Belanja & Self-Reward', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Top up game mobile legends 50000');
    
    expect($result)->toBe([
        'group' => 'Wants',
        'sub_category' => 'Belanja & Self-Reward',
    ]);
});

test('it classifies saham/crypto as Savings - Tabungan & Investasi', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Beli crypto bitcoin 100000');
    
    expect($result)->toBe([
        'group' => 'Savings',
        'sub_category' => 'Tabungan & Investasi',
    ]);
});

test('it falls back to Lainnya for unknown words', function () {
    $service = new CategoryClassifierService();
    $result = $service->classify('Beli sesuatu yang aneh 200000');
    
    expect($result)->toBe([
        'group' => 'Lainnya',
        'sub_category' => 'Belum Dikategorikan',
    ]);
});
