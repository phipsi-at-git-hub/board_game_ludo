<?php
// src/Core/Date/DateRange.php

namespace App\Core\Date;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use DateTimeInterface;

final class DateRange {
    private ?DateTimeImmutable $start;
    private ?DateTimeImmutable $end;

    /**
     * @param DateTimeInterface[] $dates
     */
    public function __construct(array $dates = []) {
        $this->start = null;
        $this->end = null;

        if (empty($dates)) {
            return;
        }

        $normalized = [];

        foreach ($dates as $date) {
            if (!$date instanceof DateTimeInterface) {
                continue;
            }
            $normalized[] = DateTimeImmutable::createFromInterface($date);
        }

        if (empty($normalized)) {
            return;
        }

        usort(
            $normalized,
            static fn(DateTimeImmutable $a, DateTimeImmutable $b) => $a <=> $b
        );

        $this->start = $normalized[0];
        $this->end = $normalized[array_key_last($normalized)];
    }

    /**
     * Returns whether the range contains any valid dates.
     */
    public function isEmpty(): bool {
        return $this->start === null || $this->end === null;
    }

    /**
     * Returns the start of the range.
     */
    public function getStart(): ?DateTimeImmutable {
        return $this->start;
    }

    /**
     * Returns the end of the range.
     */
    public function getEnd(): ?DateTimeImmutable {
        return $this->end;
    }

    /**
     * Returns true if the given timestamp lies inside the range (inclusive).
     */
    public function contains(DateTimeInterface|string $date): bool {
        if ($this->isEmpty()) {
            return false;
        }

        if (is_string($date)) {
            $date = new DateTimeImmutable($date); 
        }

        $timestamp = $date->getTimestamp();

        return
            $timestamp >= $this->start->getTimestamp()
            && $timestamp <= $this->end->getTimestamp();
    }

    /**
     * Returns true if the range spans exactly one calendar day.
     */
    public function isSingleDay(): bool {
        if ($this->isEmpty()) {
            return false;
        }

        return
            $this->start->format('Y-m-d')
            ===
            $this->end->format('Y-m-d');
    }

    /**
     * Returns all calendar days touched by this range.
     *
     * The time component is ignored.
     */
    public function getDays(): DatePeriod {
        if ($this->isEmpty()) {
            return new DatePeriod(
                new DateTimeImmutable('1970-01-01'),
                new DateInterval('P1D'),
                new DateTimeImmutable('1970-01-01')
            );
        }

        $start = $this->start->setTime(0, 0, 0);

        $end = $this->end
            ->setTime(0, 0, 0)
            ->modify('+1 day');

        return new DatePeriod(
            $start,
            new DateInterval('P1D'),
            $end
        );
    }
}
