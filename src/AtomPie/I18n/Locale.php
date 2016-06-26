<?php
namespace AtomPie\I18n {

    class Locale
    {

        /**
         * @var string
         */
        private static $sLanguage;

        /**
         * @var Engine
         */
        private static $oEngine;

        /**
         * @var bool
         */
        private static $bGetTextSetupDone;

        /**
         * @return Engine
         */
        public static function getI18nEngine()
        {
            return (isset(self::$oEngine))
                ? self::$oEngine
                : new Engine(Engine::GET_TEXT);
        }

        /**
         * @param Engine $oEngine
         */
        public static function setI18nEngine(Engine $oEngine)
        {
            self::$oEngine = $oEngine;
        }

        /**
         * @param string $sLanguage
         * @param $sLocaleFolder
         * @param string $sLocaleFile
         * @throws Exception
         */
        public static function setLanguage($sLanguage, $sLocaleFolder = '/tmp', $sLocaleFile = 'default')
        {
            self::$sLanguage = $sLanguage;

            if (!self::$bGetTextSetupDone && self::getI18nEngine()->isGetTextBased()) {

                if (!function_exists("gettext")) {
                    throw new Exception('Could not use [gettext] function as GetText extension not installed.');
                }

                $sLocale = self::getLanguage();;  // the sLocale you want
                $sLocalesRoot = realpath($sLocaleFolder);  // locales directory
                $sDomain = $sLocaleFile; // this is the .PO/.MO file name without the extension

                // Activate the setting
                if (!setlocale(LC_ALL, $sLocale)) {
                    throw new Exception(
                        sprintf(
                            'Function setlocale failed: locale function is not available on this platform, or %s local does not exist in this environment. Use i.e: sudo locale-gen pl_PL',
                            $sLocale
                        )
                    );
                }
                if (!setlocale(LC_TIME, $sLocale)) {
                    throw new Exception(
                        sprintf(
                            'Function setlocale failed: locale function is not available on this platform, or %s local does not exist in this environment. Use i.e: sudo locale-gen pl_PL',
                            $sLocale
                        )
                    );
                }
                if (!putenv("LANG=$sLocale")) {
                    throw new Exception('Function [putenv] failed.');
                }

                // Path to the .MO file that we should monitor
                $sFileName = "$sLocalesRoot/$sLocale/LC_MESSAGES/$sDomain.mo";
                if (is_file($sFileName)) {
                    $sModificationTime = filemtime($sFileName); // check its modification time
                    // Our new unique .MO file
                    $sNewFileName = "$sLocalesRoot/$sLocale/LC_MESSAGES/cache_{$sModificationTime}.mo";

                    if (!file_exists($sNewFileName)) {  // check if we have created it before
                        // If not, create it now, by copying the original
                        copy($sFileName, $sNewFileName);
                    }
                }

                if (isset($sModificationTime)) {
                    // Compute the new sDomain name
                    $sNewDomain = "cache_{$sModificationTime}";
                } else {
                    $sNewDomain = $sDomain;
                }

                // Bind it
                bindtextdomain($sNewDomain, $sLocalesRoot);
                // Encoding
                bind_textdomain_codeset($sNewDomain, 'UTF-8');
                // Then activate it
                textdomain($sNewDomain);

                self::$bGetTextSetupDone = true;
            }
        }

        /**
         * @return string
         */
        public static function getLanguage()
        {
            return isset(self::$sLanguage)
                ? self::$sLanguage
                : 'en_GB';
        }

        /**
         * @return bool
         */
        public static function hasLanguage()
        {
            return isset(self::$sLanguage);
        }
    }

}
