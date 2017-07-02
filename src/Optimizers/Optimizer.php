<?php

namespace Spatie\ImageOptimizer\Optimizers;

interface Optimizer
{
    public function setImagePath(string $imagePath);

    public function setOptions(array $options = []);

    public function getCommand();

    public function canHandle(string $mimeType): bool;

    public function binaryName(): string;
}