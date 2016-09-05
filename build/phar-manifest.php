#!/usr/bin/env php
<?php
print 'phpdraft/phpdraft: ';

$tag = @exec('git describe --tags 2>&1');

if (strpos($tag, '-') === false && strpos($tag, 'No names found') === false) {
    print $tag;
} else {
    $branch = @exec('git rev-parse --abbrev-ref HEAD');
    $hash   = @exec('git log -1 --format="%H"');
    print $branch . '@' . $hash;
}

print "\n";
