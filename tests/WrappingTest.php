<?php

use nostriphant\NIP59\Gift;
use nostriphant\NIP01\Key;
use nostriphant\NIP59\Seal;
use nostriphant\NIP01\Rumor;

it('wraps message in a seal and seal in a gift', function () {
    $sender_key = Key::fromHex('a71a415936f2dd70b777e5204c57e0df9a6dffef91b3c78c1aa24e54772e33c3');
    $sender_pubkey = $sender_key(Key::public());
    $recipient_key = Key::fromHex('6eeb5ad99e47115467d096e07c1c9b8b41768ab53465703f78017204adc5b0cc');
    $recipient_pubkey = $recipient_key(Key::public());

    $message = new Rumor(
            pubkey: $sender_pubkey,
            created_at: time(),
            kind: 14,
            content: 'Hello!!',
            tags: [['p', $recipient_pubkey]]
    );
    $seal = Seal::close($sender_key, $recipient_pubkey, $message);
    $gift = Gift::wrap($recipient_pubkey, $seal);
    expect($gift->kind)->toBe(1059);

    // sending ...

    $seal = Gift::unwrap($recipient_key, $gift);
    expect($seal->kind)->toBe(13);
    expect($seal->pubkey)->toBeString();
    expect($seal->content)->toBeString();

    $private_message = Seal::open($recipient_key, $seal);
    expect($private_message)->toHaveKey('id');
    expect($private_message)->toHaveKey('content');
    expect($private_message->content)->toBe('Hello!!');
});
