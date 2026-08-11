<?php
// src/Core/Date/DateRange.php

namespace App\Core\Date;

use App\Constants\Application;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

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
            if ($date instanceof DateTimeInterface) {
                $normalized[] = DateTimeImmutable::createFromInterface($date);
            }
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
     * 
     * isEmpty
     *
     * @return bool
     */
    public function isEmpty(): bool {
        return $this->start === null;
    }

    /**
     * Returns the start of the range.
     * 
     * getStart
     *
     * @return DateTimeImmutable
     */
    public function getStart(): ?DateTimeImmutable {
        return $this->start;
    }

    /**
     * Returns the end of the range.
     * 
     * getEnd
     *
     * @return DateTimeImmutable
     */
    public function getEnd(): ?DateTimeImmutable {
        return $this->end;
    }

    /**
     * Returns a formatted date range string by start and end date and time
     * 
     * getDateRangeAsString
     *
     * @return String
     */
    public function getDateRangeAsString(): String {
        return $this->start->format(Application::FILE_DATE_TIME_FORMAT) . ' - ' . $this->end->format(Application::FILE_DATE_TIME_FORMAT); 
    }

    /**
     * Created a date range from a formatted date range string. 
     * 
     * @param string $formattedString -> format: start date & time - end date & time -> yyyy-mm-dd hh:mm:ss - yyyy-mm-dd hh:mm:ss
     * @return self
     */
    public static function fromString(string $formattedString): self {
        $parts = explode(' - ', $formattedString, 2); 
        if (count($parts) !== 2) {
            return new self(); 
        }

        try {
            return new self([
                new DateTimeImmutable($parts[0]), 
                new DateTimeImmutable($parts[1]), 
            ]); 
        } catch (Exception) {
            return new self(); 
        }
    }

    /**
     * Returns true if the given timestamp lies inside the range (inclusive).
     * 
     * contains
     *
     * @param  mixed $date
     * @return bool
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
     * 
     * isSingleDay
     *
     * @return bool
     */
    public function isSingleDay(): bool {
        if ($this->isEmpty()) {
            return false;
        }

        return
            $this->start->format(Application::FILE_DATE_FORMAT)
            ===
            $this->end->format(Application::FILE_DATE_FORMAT);
    }

    /**
     * Returns all calendar days touched by this range.
     *
     * The time component is ignored.
     * 
     * getDays
     *
     * @return iterable
     */
    public function getDays(): iterable {
        if ($this->isEmpty()) {
            return; 
        }
        
        $current = $this->start->setTime(0, 0, 0); 
        $last = $this->end->setTime(0, 0, 0,); 

        while ($current <= $last) {
            yield $current; 
            $current = $current->modify('+1 day'); 
        }
    }
}
