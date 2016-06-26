<?php
namespace AtomPie\Html {

    use AtomPie\Html\Tag\Script;

    /**
     * Class ScriptsCollection.
     *
     * Holds unique references to scripts.
     *
     * @package AtomPie\Html
     */
    class ScriptsCollection
    {

        private $aAddedScripts = array();

        /**
         * @param string $sScriptPath
         * @return boolean
         */
        public function hasScript($sScriptPath)
        {
            return isset($this->aAddedScripts[$sScriptPath]);
        }

        /**
         * @param string $sScriptPath
         * @return ElementNode
         */
        public function getScript($sScriptPath)
        {
            if ($this->hasScript($sScriptPath)) {
                return $this->aAddedScripts[$sScriptPath];
            }
            return null;
        }

        /**
         * @param $sKeyPath
         */
        public function removeScript($sKeyPath)
        {
            unset($this->aAddedScripts[$sKeyPath]);
        }

        /**
         * @return array
         */
        public function getScripts()
        {
            return $this->aAddedScripts;
        }

        /**
         * @param string $sScriptPath
         * @param string $sContent
         * @param bool $bOnTop
         * @param null $onCondition
         * @return Script
         */
        public function addScript($sScriptPath = null, $sContent = null, $bOnTop = false, $onCondition = null)
        {

            $oScript = $this->prepareScript($sScriptPath, $sContent, $onCondition);

            if ($bOnTop) {
                $aTop = array();
                if (!is_null($sScriptPath)) {
                    $aTop[$sScriptPath] = $oScript;
                } else {
                    if (!empty($sContent)) {
                        $aTop[md5($sContent)] = $oScript;
                    }
                }
                $this->aAddedScripts = $aTop + $this->aAddedScripts;
            } else {
                if (!is_null($sScriptPath)) {
                    $this->aAddedScripts[$sScriptPath] = $oScript;
                } else {
                    if (!empty($sContent)) {
                        $this->aAddedScripts[md5($sContent)] = $oScript;
                    }
                }
            }

            return $oScript;
        }

        /**
         * @param string $sScriptPath
         * @param string $sContent
         * @param string $sContentKey
         */
        public function appendContentToScript($sScriptPath, $sContent, $sContentKey = null)
        {
            $this->getScript($sScriptPath)->addChild(new TextNode($sContent), $sContentKey);
        }

        private function prepareScript($sScriptPath = null, $sContent = null, $onCondition = null)
        {
            $oScript = new Script($sScriptPath, $sContent);
            $oScript->onCondition($onCondition);

            return $oScript;
        }

        public function __toString()
        {
            return implode(PHP_EOL, $this->aAddedScripts);
        }

    }

}
