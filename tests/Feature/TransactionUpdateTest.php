<?php

use App\Models\User;
use App\Models\Transaction;

test('authenticated user can update transaction details', function () {
    $user = User::factory()->create();
    $transaction = Transaction::create([
        'user_id' => $user->id,
        'type' => 'expense',
        'amount' => 15000,
        'description' => 'Beli bakso porsi kecil',
        'category_group' => 'Needs',
        'sub_category' => 'Makan & Minum',
        'transaction_date' => now()
    ]);

    $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'description' => 'Beli bakso porsi jumbo',
        'amount' => 25000,
        'type' => 'expense',
        'category_group' => 'Wants',
        'sub_category' => 'Nongkrong & Hiburan'
    ]);

    $response->assertRedirect(route('dashboard'));
    
    $transaction->refresh();
    expect($transaction->description)->toBe('Beli bakso porsi jumbo');
    expect($transaction->amount)->toEqual(25000);
    expect($transaction->category_group)->toBe('Wants');
    expect($transaction->sub_category)->toBe('Nongkrong & Hiburan');
});

test('authenticated user cannot update transaction of another user', function () {
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();
    
    $transaction = Transaction::create([
        'user_id' => $anotherUser->id,
        'type' => 'expense',
        'amount' => 15000,
        'description' => 'Beli nasi uduk',
        'category_group' => 'Needs',
        'sub_category' => 'Makan & Minum',
        'transaction_date' => now()
    ]);

    $response = $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'description' => 'Beli nasi uduk enak',
        'amount' => 20000,
        'type' => 'expense',
        'category_group' => 'Needs',
        'sub_category' => 'Makan & Minum'
    ]);

    $response->assertStatus(403);
});
