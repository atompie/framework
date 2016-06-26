<?php
namespace AtomPie\View {

    use AtomPie\View\Boundary\IReadContent;
    use Generi\Boundary\IStringable;
    use AtomPie\View\Boundary\ICanBeRendered;
    use AtomPie\I18n\Label;
    use AtomPie\System\IO\File;

    class TwigLikeView
    {

        private $sTemplatesFolder;

        public function __construct($sTemplatesFolder)
        {
            $this->sTemplatesFolder = $sTemplatesFolder;
        }

        public function renderTemplate(array $aPlaceHolderData, IReadContent $oFile)
        {
            return $this->fillTemplate($aPlaceHolderData, $oFile->read());
        }

        /**
         * @param $aPlaceHolderData
         * @param $sTemplateContent
         * @return string
         * @throws Exception
         */
        public function fillTemplate(array $aPlaceHolderData, $sTemplateContent)
        {

            $sTemplateContent = $this->fillBlock(
                $aPlaceHolderData,
                $sTemplateContent,
                false);

            $sTemplateContent = $this->fillForEach(
                $aPlaceHolderData,
                $sTemplateContent,
                true);

            return $this->fillPlaceHolders(
                $aPlaceHolderData,
                $sTemplateContent,
                false);
        }

        private function makeArrayIntoDots($aPlaceHolders, array &$aData, $sPrefix = '', $bWithArrays = false)
        {
            if (is_array($aPlaceHolders)) {

                foreach ($aPlaceHolders as $sKey => $mPlaceHolderValue) {

                    if (is_array($mPlaceHolderValue)) {
                        if (empty($sPrefix) && $bWithArrays) {
                            $aData = $this->makeArrayIntoDots($mPlaceHolderValue, $aData,
                                $this->getPrefix($sPrefix, $sKey));
                        } else {
                            continue;
                        }
                    } else {
                        if ($mPlaceHolderValue instanceof ICanBeRendered) {
                            if (empty($sPrefix)) {
                                $aData[$sKey] = $mPlaceHolderValue;
                            }
                            $aData = $this->makeArrayIntoDots($mPlaceHolderValue->getViewPlaceHolders(), $aData,
                                $this->getPrefix($sPrefix, $sKey));
                        } else {
                            if (is_object($mPlaceHolderValue)) {
                                $aData = $this->makeArrayIntoDots(get_object_vars($mPlaceHolderValue), $aData,
                                    $this->getPrefix($sPrefix, $sKey));
                            } else {
                                $aData[$this->getPrefix($sPrefix, $sKey)] = $mPlaceHolderValue;
                            }
                        }
                    }

                }

            }

            return $aData;

        }

        private function getPrefix($sPrefix, $sKey)
        {
            if ($sPrefix == '') {
                return $sKey;
            }
            return $sPrefix . '.' . $sKey;
        }

        /**
         * @param $sTemplateContent
         * @param array $PlaceholderData
         * @param CollectionItem $oItem
         * @return string
         */
        private function replacePlaceHolders($sTemplateContent, array $PlaceholderData, CollectionItem $oItem = null)
        {

            $aFlatData = array();
            $aFlatPlaceholderData = $this->makeArrayIntoDots(
                $PlaceholderData,
                $aFlatData,
                '',
                $oItem !== null
            );

            return \preg_replace_callback(
                '/{{\s*([a-zA-Z0-9_\.]+)\s*}}/',
                function ($aMatch) use ($aFlatPlaceholderData, $oItem) {

                    list($sPlaceHolder, $sValue) = $aMatch;

                    // Find content
                    if ($oItem !== null and $oItem->getItemName() == $sValue) {
                        $mContent = $oItem->getComponent();
                    } else {
                        if (isset($aFlatPlaceholderData[$sValue])) {
                            $mContent = $aFlatPlaceholderData[$sValue];
                        } else {
                            $mContent = $sPlaceHolder;
                        }
                    }

                    // Replace place holders

                    if ($mContent instanceof ICanBeRendered) {
                        $oTemplate = new TwigTemplateFile($mContent->getTemplateFile($this->sTemplatesFolder));
                        return $this->renderTemplate($mContent->getViewPlaceHolders(), $oTemplate);
                    } else {
                        if ($mContent instanceof IStringable) {
                            return $mContent->__toString();
                        } else {
                            if (is_array($mContent)) {
                                return $sPlaceHolder; // Array can not be made toString.
                            } else {
                                return $mContent;
                            }
                        }
                    }

                },
                $sTemplateContent
            );

        }

        private function fillBlock(array $aPlaceHolderData, $sTemplateContent, $bIsCollection)
        {
            $sRegExp = '/\{%\s*block(.*?)%\}(.*?)\{%\s*endblock\s*%\}/si';
            $pClosure = function ($aMatch) {
                return array($aMatch[1], $aMatch[1], $aMatch[2]);
            };
            return $this->fillCommand($sRegExp, $aPlaceHolderData, $sTemplateContent, $bIsCollection, $pClosure);
        }

        private function fillForEach(array $aPlaceHolderData, $sTemplateContent, $bIsCollection)
        {
            $sRegExp = '/\{%\s*for(.*?)\s*in\s*(.*?)%\}(.*?)\{%\s*endfor\s*%\}/si';

            $pClosure = function ($aMatch) {
                return array($aMatch[1], $aMatch[2], $aMatch[3]);
            };

            return $this->fillCommand($sRegExp, $aPlaceHolderData, $sTemplateContent, $bIsCollection, $pClosure);
        }

        /**
         * @param $sRegExp
         * @param array $aPlaceHolderData
         * @param $sTemplateContent
         * @param $bIsCollection
         * @param $pClosure
         * @return string
         * @throws Exception
         */
        private function fillCommand(
            $sRegExp,
            array $aPlaceHolderData,
            $sTemplateContent,
            $bIsCollection,
            $pClosure
        ) {

            $iCount = preg_match_all($sRegExp, $sTemplateContent, $aMatch);

            if ($iCount > 0) {

                $aStringsToReplace = $aMatch[0];
                list($aItemNames, $aCollectionNames, $aBlockContents) = $pClosure($aMatch);
                $aReplacement = array();
                $aToReplace = array();

                // Replace placeholders
                foreach ($aCollectionNames as $iPosition => $sCollectionName) {

                    $sCollectionName = trim($sCollectionName);
                    $sCollectionItemName = trim($aItemNames[$iPosition]);

                    if (isset($aPlaceHolderData[$sCollectionName])) {

                        // Data available

                        $aToReplace[] = $aStringsToReplace[$iPosition];
                        $aReplacement[] = rtrim($this->fillPlaceHolders(
                            $aPlaceHolderData,
                            ltrim($aBlockContents[$iPosition]), // Inner template
                            $bIsCollection,
                            $sCollectionName,
                            $sCollectionItemName)
                        );

                    } else {

                        //Replace with empty string if data not available

                        $aToReplace[] = $aStringsToReplace[$iPosition];
                        $aReplacement[] = '';
                    }

                }


                if (!empty($aReplacement)) {
                    $sTemplateContent = str_replace($aToReplace, $aReplacement, $sTemplateContent);
                }

            }

            return $sTemplateContent;
        }

        /**
         * @param array $aPlaceholderData
         * @param $sTemplateContent
         * @param $bIsCollection
         * @param string $sCollectionName
         * @param string $sCollectionItemName
         * @return string
         * @throws Exception
         */
        private function fillPlaceHolders(
            array $aPlaceholderData,
            $sTemplateContent,
            $bIsCollection,
            $sCollectionName = null,
            $sCollectionItemName = null
        ) {

            if ($bIsCollection) {

                // Collection of templates
                if ($sCollectionName !== null && isset($aPlaceholderData[$sCollectionName])) {
                    $aCollection = $aPlaceholderData[$sCollectionName];
                } else {
                    $aCollection = $aPlaceholderData;
                }

                if (!is_array($aCollection)) {
                    throw new Exception(new Label('Block data should be tied to array of values.'));
                }

                $sResult = '';
                foreach ($aCollection as $mItem) {

                    if ($mItem instanceof ICanBeRendered) {
                        // IF collection - must have name
                        $oCollectionItem = new CollectionItem($sCollectionName, $sCollectionItemName,
                            $mItem->getViewPlaceHolders(), $mItem);
                    } else {
                        if (is_array($mItem)) {
                            $oCollectionItem = new CollectionItem($sCollectionName, $sCollectionItemName, $mItem);
                        } else {
                            continue;
                        }
                    }

                    $sResult .= $this->replacePlaceHolders(
                        $sTemplateContent,
                        array(
                            $sCollectionItemName => $oCollectionItem->getProperties()
                        ),
                        $oCollectionItem
                    );
                }

                return $sResult;

            } else {

                // Single template
                if (isset($aPlaceholderData[$sCollectionName]) and is_array($aPlaceholderData[$sCollectionName])) {
                    $oCollectionItem = new CollectionItem($sCollectionName, $sCollectionItemName, $aPlaceholderData);
                    return $this->replacePlaceHolders($sTemplateContent, $aPlaceholderData, $oCollectionItem);
                }

                return $this->replacePlaceHolders($sTemplateContent, $aPlaceholderData);

            }

        }

    }

}
