<?php

declare(strict_types=1);

use App\Livewire\Chat\ChatPage;
use App\Models\Message;
use App\Models\User;
use App\Services\Chat\MessagingService;
use Livewire\Livewire;

beforeEach(function () {
    $this->me = User::factory()->create();
    $this->other = User::factory()->create(['name' => 'Other Person']);
    $this->actingAs($this->me);

    $this->conv = app(MessagingService::class)->openDirect($this->me, $this->other);
});

it('renders the chat shell with the active conversation', function () {
    Livewire::test(ChatPage::class, ['conversation' => $this->conv->id])
        ->assertSee('Other Person')
        ->assertSet('activeConversationId', $this->conv->id);
});

it('sends a message and persists it', function () {
    Livewire::test(ChatPage::class, ['conversation' => $this->conv->id])
        ->set('body', 'Hello there about marketplace work')
        ->call('send')
        ->assertHasNoErrors();

    expect(Message::query()->where('conversation_id', $this->conv->id)->count())->toBe(1);
});

it('marks messages read when opening', function () {
    app(MessagingService::class)->send($this->conv, $this->other, 'Ping');

    Livewire::test(ChatPage::class)
        ->call('open', $this->conv->id);

    $latest = Message::query()->where('conversation_id', $this->conv->id)->where('user_id', $this->other->id)->first();
    expect($latest->is_read)->toBeTrue();
});
