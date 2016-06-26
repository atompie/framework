<?php
namespace AtomPie\I18n {

    use Generi\Text;

    class Label extends Text
    {

        /**
         * @return string
         */
        final public function __toString()
        {

            try {
                $sString = parent::__toString();
                return gettext($sString);
            } catch (\Exception $e) {
                echo $e->__toString();
                exit;
            }

        }
    }
}
