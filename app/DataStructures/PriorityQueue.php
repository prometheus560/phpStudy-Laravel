<?php

namespace App\DataStructures;

/**
 * PRIORITY QUEUE
 * Items leave in order of priority: High, then Medium, then Low.
 * Items with the same priority leave in the order they arrived.
 */
class PriorityQueue
{
    private array $high = [];
    private array $medium = [];
    private array $low = [];
    private array $log = [];

    // Add an item to the group that matches its priority
    public function enqueue($item, string $priority): void
    {
        if ($priority === 'High') {
            $this->high[] = $item;
        } elseif ($priority === 'Medium') {
            $this->medium[] = $item;
        } else {
            $this->low[] = $item;
        }

        $this->record('enqueue', $item, $priority);
    }

    // Remove and return the most important item
    public function dequeue()
    {
        if (count($this->high) > 0) {
            $priority = 'High';
            $item = array_shift($this->high);
        } elseif (count($this->medium) > 0) {
            $priority = 'Medium';
            $item = array_shift($this->medium);
        } elseif (count($this->low) > 0) {
            $priority = 'Low';
            $item = array_shift($this->low);
        } else {
            return null;
        }

        $this->record('dequeue', $item, $priority);

        return $item;
    }

    public function isEmpty(): bool
    {
        return $this->size() === 0;
    }

    public function size(): int
    {
        return count($this->high) + count($this->medium) + count($this->low);
    }

    // Everything in priority order
    public function toArray(): array
    {
        return array_merge($this->high, $this->medium, $this->low);
    }

    // The three groups, for display
    public function groups(): array
    {
        return ['High' => $this->high, 'Medium' => $this->medium, 'Low' => $this->low];
    }

    public function trace(): array
    {
        return $this->log;
    }

    private function record(string $op, $item, string $priority): void
    {
        $this->log[] = [
            'op'       => $op,
            'item'     => is_object($item) && isset($item->task_name) ? $item->task_name : (string) $item,
            'priority' => $priority,
            'size'     => $this->size(),
        ];
    }
}