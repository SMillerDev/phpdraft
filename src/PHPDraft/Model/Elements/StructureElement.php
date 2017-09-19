<?php
/**
 * This file contains the StructureElement.php.
 *
 * @package PHPDraft\Model\Elements
 *
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

interface StructureElement
{
    /**
     * Default datatypes.
     *
     * @var array
     */
    const DEFAULTS = ['boolean', 'string', 'number', 'object', 'array', 'enum'];

    /**
     * Parse a JSON object to a structure.
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return self self reference
     */
    public function parse($object, &$dependencies);

    /**
     * Print a string representation.
     *
     * @return string
     */
    public function __toString();
}
