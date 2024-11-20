<?php

namespace nostriphant\NIP59;

use nostriphant\NIP01\Nostr;
use nostriphant\NIP01\Key;
use nostriphant\NIP01\Event;
use nostriphant\NIP44\Encrypt,
    nostriphant\NIP44\Decrypt;

class Seal {
    
    static function close(Key $sender_private_key, string $recipient_pubkey, Rumor $event) : Event {
        $encrypter = Encrypt::make($sender_private_key, $recipient_pubkey);
        $seal = new Rumor(
            pubkey: $sender_private_key(Key::public()),
            created_at: mktime(rand(0,23), rand(0,59), rand(0,59)), 
            kind: 13, 
            content: $encrypter(Nostr::encode(get_object_vars($event))), 
            tags: []
        );
        return $seal($sender_private_key);
    }
    
    static function open(Key $recipient_private_key, Event $seal): Rumor {
        $decrypter = Decrypt::make($recipient_private_key, $seal->pubkey);
        return Rumor::__set_state(Nostr::decode($decrypter($seal->content)));
    }
}
