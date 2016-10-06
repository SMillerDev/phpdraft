<?php
/**
 * This file contains the ${FILE_NAME}
 *
 * @package php-drafter\SOMETHING
 * @author  Sean Molenaar<sean@seanmolenaar.eu>
 */

namespace PHPDraft\Model\Elements;

use Michelf\Markdown;
use PHPDraft\Model\StructureElement;

class EnumStructureElement implements StructureElement
{
    /**
     * Object description
     * @var string
     */
    public $description;
    /**
     * Type of element
     * @var string
     */
    public $element = NULL;
    /**
     * Object value
     * @var mixed
     */
    public $value = NULL;
    /**
     * Object status (required|optional)
     * @var string
     */
    public $status = '';
    /**
     * List of object dependencies
     * @var string[]
     */
    public $deps;

    /**
     * Parse a JSON object to a structure
     *
     * @param \stdClass $item       An object to parse
     * @param array     $dependencies Dependencies of this object
     *
     * @return EnumStructureElement self reference
     */
    function parse($item, &$dependencies)
    {
        $this->element = (isset($item->element)) ? $item->element : NULL;
        $this->description = (isset($item->meta->description)) ? $item->meta->description : NULL;
        $this->value = (isset($item->content)) ? $item->content : NULL;
        $this->description_as_html();

        if (!in_array($this->element, self::DEFAULTS))
        {
            $dependencies[] = $this->element;
        }

        return $this;
    }

    /**
     * Print a string representation
     *
     * @return string
     */
    function __toString()
    {
        $type = (!in_array($this->element, self::DEFAULTS)) ?
            '<a class="code" href="#object-' . str_replace(' ', '-', strtolower($this->element)) . '">' . $this->element . '</a>' : '<code>' . $this->element . '</code>';
        $return = '<tr>' .
                    '<td><span>' . $this->value . '</span></td>' .
                    '<td>'.$type.'</td>' .
                    '<td><span>' . $this->description . '</span></td>' .
                  '</tr>';
        return $return;
    }

    /**
     * Parse the description to HTML
     *
     * @return string
     */
    public function description_as_html()
    {
        $this->description = Markdown::defaultTransform($this->description);
    }
}