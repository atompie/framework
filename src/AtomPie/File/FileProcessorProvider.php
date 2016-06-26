<?php
namespace AtomPie\File {

    use AtomPie\Boundary\Core\Dispatch\IAmDispatchManifest;
    use AtomPie\Boundary\Core\IRegisterContentProcessors;
    use AtomPie\Boundary\Core\ISetUpContentProcessor;
    use AtomPie\System\IO\File;

    class FileProcessorProvider implements ISetUpContentProcessor
    {

        /**
         * Runs before configureProcessor method.
         * Sets DispatchManifest.
         *
         * @param IAmDispatchManifest $oDispatchManifest
         */
        public function init(IAmDispatchManifest $oDispatchManifest)
        {
            // Not used
        }

        public function configureProcessor(IRegisterContentProcessors $oContentProcessor)
        {

            $oContentProcessor->registerAfter(File::class,
                function (File $oFileAsContent) {
                    return $oFileAsContent->loadRaw();
                });

        }
    }

}
