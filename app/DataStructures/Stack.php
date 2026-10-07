<?php

namespace App\DataStructures;

/**
 * STACK - LIFO (Last In, First Out)
 * Items are pushed on top and popped from the top.
 */
class Stack
{
    private array $items = [];
    private array $log = [];

    // Put an item on top
    public function push($item): void
    {
        $this->items[] = $item;
        $this->record('push', $item);
    }

    // Remove and return the top item
    public function pop()
    {
        if ($this->isEmpty()) {
            return null;
        }

        $item = array_pop($this->items);
        $this->record('pop', $item);

        return $item;
    }

    // Look at the top item without removing it
    public function peek()
    {
        return $this->isEmpty() ? null : $this->items[count($this->items) - 1];
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function size(): int
    {
        return count($this->items);
    }

    // Bottom -> top
    public function toArray(): array
    {
        return $this->items;
    }

    public function trace(): array
    {
        return $this->log;
    }

    private function record(string $op, $item): void
    {
        $this->log[] = [
            'op'   => $op,
            'item' => is_object($item) && isset($item->task_name) ? $item->task_name : (string) $item,
            'size' => count($this->items),
        ];
    }
}