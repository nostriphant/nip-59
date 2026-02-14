<?php

namespace nostriphant\NIP59;

use nostriphant\NIP01\Nostr;
use nostriphant\NIP01\Key;
use nostriphant\NIP01\Event;
use nostriphant\NIP01\Rumor;
use nostriphant\NIP44\Encrypt,
    nostriphant\NIP44\Decrypt;

class Gift {
    
    static function wrap(string $recipient_pubkey, Event $event) : Event {
        $randomKey = Key::generate();
        $encrypter = Encrypt::make($randomKey, $recipient_pubkey);
        $gift = new Rumor(
            pubkey: $randomKey(Key::public()),
            created_at: time() - rand(0, 60 * 60 * 48),
                kind: 1059,
                content: $encrypter(Nostr::encode(get_object_vars($event))), 
            tags: [['p', $recipient_pubkey]]
        );
        return $gift($randomKey);
    }
    
    static function unwrap(Key $recipient_key, Event $gift): Event {
        $decrypter = Decrypt::make($recipient_key, $gift->pubkey);
        return Event::__set_state(Nostr::decode($decrypter($gift->content)));
    }
    
}
