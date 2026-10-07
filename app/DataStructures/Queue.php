<?php

namespace App\DataStructures;

/**
 * QUEUE - FIFO (First In, First Out)
 * Items join at the rear and leave from the front.
 */
class Queue
{
    private array $items = [];
    private array $log = [];

    // Add an item at the rear
    public function enqueue($item): void
    {
        $this->items[] = $item;
        $this->record('enqueue', $item);
    }

    // Remove and return the item at the front
    public function dequeue()
    {
        if ($this->isEmpty()) {
            return null;
        }

        $item = array_shift($this->items);
        $this->record('dequeue', $item);

        return $item;
    }

    // Look at the front item without removing it
    public function peek()
    {
        return $this->items[0] ?? null;
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function size(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    // Every operation performed, in order (shown on the page)
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