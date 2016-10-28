<?php
/**
 * This file contains the StructureElement.php
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model;


interface StructureElement
{
    /**
     * Default datatypes
     *
     * @var array
     */
    const DEFAULTS = ['boolean', 'string', 'number', 'object', 'array', 'enum'];

    /**
     * Parse a JSON object to a structure
     *
     * @param \stdClass $object       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return StructureElement self reference
     */
    function parse($object, &$dependencies);

    /**
     * Print a string representation
     *
     * @return string
     */
    function __toString();
}