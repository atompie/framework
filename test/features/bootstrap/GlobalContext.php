<?php
namespace {

    use Behat\Behat\Context\Context;
    use WorkshopTest\Boot;
    use WorkshopTest\Resource\Config\Config;

    class GlobalContext implements Context
    {
        /**
         * @var \AtomPie\System\Application
         */
        protected $oApplication;

        /**
         * @param $endPointSpec
         * @param $namespace
         * @return \AtomPie\System\Application
         */
        protected function getApp($endPointSpec, $namespace = null)
        {

            $oConfig = Config::get();
            return Boot::up($oConfig, $endPointSpec, $namespace);
        }
    }

}
