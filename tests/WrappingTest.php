<?php

use nostriphant\NIP59\Gift;
use nostriphant\NIP01\Key;
use nostriphant\NIP59\Seal;
use nostriphant\NIP59\Rumor;

it('wraps message in a seal and seal in a gift', function () {
    $private_key = \Pest\key_sender();
    $recipient_key = \Pest\key_recipient();
    $recipient_pubkey = $recipient_key(Key::public());

    $gift = Gift::wrap($recipient_pubkey, Seal::close($private_key, $recipient_pubkey, new Rumor(
                            pubkey: $private_key(Key::public()),
                            created_at: time(),
                            kind: 14,
                            content: 'Hello!!',
                            tags: [['p', $recipient_pubkey]]
            )));

    expect($gift->kind)->toBe(1059);

    $seal = Gift::unwrap($recipient_key, $gift->pubkey, $gift->content);
    expect($seal->kind)->toBe(13);
    expect($seal->pubkey)->toBeString();
    expect($seal->content)->toBeString();

    $private_message = Seal::open($recipient_key, $seal->pubkey, $seal->content);
    expect($private_message)->toHaveKey('id');
    expect($private_message)->toHaveKey('content');
    expect($private_message->content)->toBe('Hello!!');
});
