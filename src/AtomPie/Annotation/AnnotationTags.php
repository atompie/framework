<?php
namespace AtomPie\Annotation;

class AnnotationTags implements \ArrayAccess
{
    /**
     * @var object[]
     */
    private $aAnnotations;

    public function __construct(array $aAnnotations)
    {
        $this->aAnnotations = $aAnnotations;
    }

    /**
     * @param $sType
     * @return null | object
     */
    public function getFirstAnnotationByType($sType)
    {

        if (!empty($this->aAnnotations) && isset($this->aAnnotations[$sType])) {
            reset($this->aAnnotations[$sType]);
            return current($this->aAnnotations[$sType]);
        }

        return null;

    }

    /**
     * @param $sType
     * @return bool
     */
    public function has($sType) {
        return $this->offsetExists($sType) && !empty($this->aAnnotations[$sType]);
    }

    /**
     * @param $sType
     * @return null | object[]
     */
    public function getAnnotationsByType($sType)
    {

        if (!empty($this->aAnnotations) && isset($this->aAnnotations[$sType])) {
            return $this->aAnnotations[$sType];
        }

        return null;

    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return empty($this->aAnnotations);
    }
    
    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->aAnnotations[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
       return $this->aAnnotations[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->aAnnotations[$offset] =  $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->aAnnotations[$offset]);
    }
}