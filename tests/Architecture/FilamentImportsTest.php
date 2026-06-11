<?php

/**
 * Every Filament `use` import must either resolve to a real class/interface/trait
 * or be used as a namespace prefix (e.g. `use Filament\Actions;` → `Actions\Action::make()`).
 * Unused or unresolvable imports are errors.
 */
test('every filament import resolves and is not dead', function () {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__ . '/../../src'),
    );

    $errors = [];
    $total = 0;

    foreach ($files as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $content = file_get_contents($file->getRealPath());
        $relative = str_replace(base_path() . '/', '', $file->getRealPath());

        preg_match_all('/^use\s+(Filament\\\\[^;]+);$/m', $content, $matches);

        foreach ($matches[1] as $stmt) {
            $total++;

            // Strip "as Alias" to get the raw FQCN
            $fqcn = trim(current(explode(' as ', $stmt, 2)));

            // Last segment = short name / alias
            $short = substr($fqcn, strrpos($fqcn, '\\') + 1);
            if (stripos($stmt, ' as ') !== false) {
                $parts = explode(' as ', $stmt, 2);
                $short = trim($parts[1]);
            }

            // Code after the use line
            $after = substr($content, strpos($content, "use $stmt;") + strlen("use $stmt;"));
            $q = preg_quote($short, '~');

            $used = preg_match('~\b' . $q . '\b~', $after);

            if (! $used) {
                $errors[] = "$relative: unused import `$stmt`";
                continue;
            }

            // Used as namespace prefix (e.g. Actions\Foo) → always valid in PHP
            if (preg_match('~\b' . $q . '\\\\~', $after)) {
                continue;
            }

            // Used as a class/interface/trait reference → must exist
            if (! class_exists($fqcn) && ! interface_exists($fqcn) && ! trait_exists($fqcn)) {
                $errors[] = "$relative: `$fqcn` not found (imported but does not exist)";
            }
        }
    }

    expect($total)->toBeGreaterThan(0);
    expect($errors)->toBeEmpty(implode("\n", $errors));
});

/**
 * Overridden methods in Filament page / resource classes must have signatures
 * compatible with the parent class. Catches e.g. `Infolist $infolist` → `Schema $schema`.
 */
test('overridden filament page methods match parent signatures', function () {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(__DIR__ . '/../../src/Filament'),
    );

    $mismatches = [];

    foreach ($files as $file) {
        if (! $file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $content = file_get_contents($file->getRealPath());
        $relative = str_replace(base_path() . '/', '', $file->getRealPath());

        if (! preg_match('/^class\s+(\w+)\s+extends\s+(\w+)/m', $content, $m)) {
            continue;
        }

        $child = $m[1];
        $parent = $m[2];

        if (! class_exists($child) || ! class_exists($parent)) {
            continue;
        }

        $rc = new ReflectionClass($child);
        $rp = new ReflectionClass($parent);

        foreach ($rc->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || ! $rp->hasMethod($method->getName())) {
                continue;
            }

            $pm = $rp->getMethod($method->getName());

            if ($method->getNumberOfParameters() !== $pm->getNumberOfParameters()) {
                $mismatches[] = "$relative: $child::{$method->getName()}() parameter count differs from $parent";
                continue;
            }

            foreach ($method->getParameters() as $i => $param) {
                $pp = $pm->getParameters()[$i];

                if ($param->hasType() && $pp->hasType() && (string) $param->getType() !== (string) $pp->getType()) {
                    $mismatches[] = "$relative: $child::{$method->getName()}(\${$param->getName()}) "
                        . "type-hinted as {$param->getType()} but $parent expects {$pp->getType()}";
                }
            }

            if ($method->hasReturnType() && $pm->hasReturnType() && (string) $method->getReturnType() !== (string) $pm->getReturnType()) {
                $mismatches[] = "$relative: $child::{$method->getName()}() returns {$method->getReturnType()} but $parent expects {$pm->getReturnType()}";
            }
        }
    }

    expect($mismatches)->toBeEmpty(implode("\n", $mismatches));
});
