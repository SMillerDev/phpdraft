<?php

/**
 * This file contains the StructureElement.php.
 *
 * @package PHPDraft\Model\Elements
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use stdClass;

interface StructureElement
{
    /**
     * Default datatypes.
     *
     * @var array
     */
    public const DEFAULTS = ['boolean', 'string', 'number', 'object', 'array', 'enum'];

    /**
     * Parse a JSON object to a structure.
     *
     * @param object $object       An object to parse
     * @param array  $dependencies Dependencies of this object
     *
     * @return self self reference
     */
    public function parse(object $object, array &$dependencies): self;

    /**
     * Print a string representation.
     *
     * @return string
     */
    public function __toString(): string;


    /**
     * Get a string representation of the value.
     *
     * @return string
     */
    public function string_value();
}
