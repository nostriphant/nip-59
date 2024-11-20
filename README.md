# nip-59
Nostr NIP-59 implementation in PHP

## Usage
```
use nostriphant\NIP01\Key;
use nostriphant\NIP59\Gift;
use nostriphant\NIP59\Seal;
use nostriphant\NIP59\Rumor;

$sender_key = Key::fromHex('a71a415936f2dd70b777e5204c57e0df9a6dffef91b3c78c1aa24e54772e33c3');
$sender_pubkey = $sender_key(Key::public());
$recipient_key = Key::fromHex('6eeb5ad99e47115467d096e07c1c9b8b41768ab53465703f78017204adc5b0cc');
$recipient_pubkey = $recipient_key(Key::public());

// Message can be any event, using NIP-17 private direct message as an example
$message = new Rumor(
    pubkey: $sender_pubkey,
    created_at: time(),
    kind: 14,
    content: 'Hello!!',
    tags: [['p', $recipient_pubkey]]
);
$seal = Seal::close($sender_key, $recipient_pubkey, $message);
$gift = Gift::wrap($recipient_pubkey, $seal);

// sending ...

$seal = Gift::unwrap($recipient_key, $gift);
$private_message = Seal::open($recipient_key, $seal);

assert($private_message->content === 'Hello!!');

```