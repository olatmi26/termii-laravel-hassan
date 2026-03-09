<?php

namespace Hassan\Termii;

class TermiiMessage
{
    public ?string $to = null;
    public string $content = '';
    public ?string $from = null;
    public ?string $channel = null;

    public static function create(string $content = ''): static
    {
        return (new static)->content($content);
    }

    public function to(string $phoneNumber): static
    {
        $this->to = $phoneNumber;
        return $this;
    }

    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function from(string $senderId): static
    {
        $this->from = $senderId;
        return $this;
    }

    public function channel(string $channel): static
    {
        $this->channel = $channel;
        return $this;
    }
}
