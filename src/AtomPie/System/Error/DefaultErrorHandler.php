<?php
namespace AtomPie\System\Error {

    use AtomPie\Boundary\System\IHandleException;
    use AtomPie\Web\Boundary\IRecognizeMediaType;

    class DefaultErrorHandler implements IHandleException
    {

        public function handleException(\Exception $oException, IRecognizeMediaType $oContentType)
        {
            $oExceptionMessage = new DefaultErrorRenderer($oContentType);
            $oExceptionMessage->ErrorMessage = $oException->getMessage();
            $oExceptionMessage->StackTrace = explode("\n", $oException->getTraceAsString());
            $oExceptionMessage->Line = $oException->getLine();
            $oExceptionMessage->File = $oException->getFile();
            $oExceptionMessage->Code = $oException->getCode();
            $oExceptionMessage->ExceptionType = get_class($oException);
            $oInternalException = $oException->getPrevious();
            $oExceptionMessage->InternalException = isset($oInternalException)
                ? $oInternalException
                : 'none';

            return $oExceptionMessage->__toString();
        }

    }

}
