<?php
// Core/helpers.php

function class_basename(string $class): string {
    return basename(str_replace('\\', '/', $class));
}