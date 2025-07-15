#!/usr/bin/env php
<?php
print 'phpdraft/phpdraft: ';

$tag = @exec('git describe --tags 2>&1');

if (!str_contains($tag, '-') && !str_contains($tag, 'No names found')) {
    print $tag;
} else {
    $branch = @exec('git rev-parse --abbrev-ref HEAD');
    $hash   = @exec('git log -1 --format="%H"');
    print $branch . '@' . $hash;
}

print "\n";
