<?php
namespace AtomPie\System\Error {

    use AtomPie\Boundary\System\IHandleException;
    use AtomPie\Web\Boundary\IRecognizeMediaType;

    class DefaultErrorHandler implements IHandleException
    {

        /**
         * @var bool
         */
        private $bWithStackTrace;

        public function __construct($bWithStackTrace = true)
        {
            $this->bWithStackTrace = $bWithStackTrace;
        }

        public function handleException(\Exception $oException, IRecognizeMediaType $oContentType)
        {
            $oExceptionMessage = new DefaultErrorRenderer($oContentType);
            $oExceptionMessage->ErrorMessage = $oException->getMessage();

            $oExceptionMessage->Line = $oException->getLine();
            $oExceptionMessage->File = $oException->getFile();
            $oExceptionMessage->Code = $oException->getCode();
            
            if ($this->bWithStackTrace) {
                $oExceptionMessage->StackTrace = explode("\n", $oException->getTraceAsString());
                $oExceptionMessage->ExceptionType = get_class($oException);
                $oInternalException = $oException->getPrevious();
                $oExceptionMessage->InternalException = isset($oInternalException)
                    ? $oInternalException
                    : 'none';
            } else {
                $oExceptionMessage->StackTrace = '';
                $oExceptionMessage->ExceptionType = get_class($oException);
                $oExceptionMessage->InternalException = '';
            }
            
            return $oExceptionMessage->__toString();
        }

    }

}
