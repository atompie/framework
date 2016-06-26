<?php
namespace AtomPie\Core\Service {

    use AtomPie\Boundary\Gui\Component\IBasicAuthorize;
    use AtomPie\Annotation\AnnotationParser;
    use AtomPie\Core\Annotation\Tag\Authorize;
    use AtomPie\Core\Annotation\Tag\Client;
    use AtomPie\Core\Annotation\Tag\EndPoint;
    use AtomPie\Core\Annotation\Tag\Header;
    use AtomPie\Core\Annotation\Tag\Log;
    use AtomPie\Core\Annotation\Tag\SaveState;
    use Generi\Exception;
    use AtomPie\I18n\Label;
    use AtomPie\Web\Connection\Http\Header\Status;

    class AuthorizeAnnotationService implements IBasicAuthorize
    {

        /**
         * @param $mEndPointObject
         * @param string | null $sEndPointMethod
         * @param array $aOnAccessDeniedStrategy
         * @return bool
         * @todo make private after test refactoring
         */
        public function invokeAuthorizeAnnotation(
            $mEndPointObject,
            $sEndPointMethod = null,
            array $aOnAccessDeniedStrategy = null
        ) {

            // Set default set of Annotations
            // TODO wynieść do statycznej metody
            $aDefaultAnnotationMapping = array(
                'EndPoint' => EndPoint::class,
                'SaveState' => SaveState::class,
                'Header' => Header::class,
                'Client' => Client::class,
                'Authorize' => Authorize::class,
                'Log' => Log::class,
            );

            $oParser = new AnnotationParser();
            $aAnnotationCollection = $oParser->getAnnotationsFromObjectOrMethod(
                $aDefaultAnnotationMapping,
                $mEndPointObject,
                $sEndPointMethod
            );

            if (!empty($aAnnotationCollection[Authorize::class])) {

                $aStrategies = array(

                    'basic' => function (Authorize $oAnnotation) {
                        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
                            return false;
                        } else {
                            list($sUser, $sPassword) = explode(':', $oAnnotation->AuthToken);
                            if ($_SERVER['PHP_AUTH_USER'] != $sUser || $_SERVER['PHP_AUTH_PW'] != $sPassword) {
                                return false;
                            }
                        }
                        return true;
                    },

                    'static' => function (Authorize $oAnnotation) {
                        if (isset($oAnnotation->AuthToken) && strtolower($oAnnotation->AuthToken) == 'public') {
                            return true;
                        }
                        return false;
                    },

                    'method' => function (Authorize $oAnnotation) use ($mEndPointObject) {

                        $aStaticMethod = $this->getStaticMethodAndClass($oAnnotation->AuthToken);

                        if ($aStaticMethod === false) {
                            throw new Exception('Wrong annotation @Authorize. Authorize logic must be in format Class.Method()');
                        }

                        list($sAuthorizationClass, $sAuthorizationMethod) = $aStaticMethod;

                        if (strtolower($sAuthorizationClass) == 'this') {

                            if (!is_object($mEndPointObject)) {
                                throw new Exception(new Label('Can not call none static authorization method on static EndPoint method.'));
                            }

                            if (!method_exists($mEndPointObject, $sAuthorizationMethod)) {
                                throw new Exception(
                                    sprintf('@Authorize method: [%s.%s] does not exist in class [%s]!',
                                        $sAuthorizationClass, $sAuthorizationMethod, get_class($mEndPointObject))
                                );
                            }

                            $bAccessGranted = $mEndPointObject->$sAuthorizationMethod($oAnnotation);

                        } else {

                            if (!method_exists($sAuthorizationClass, $sAuthorizationMethod)) {
                                throw new Exception(
                                    sprintf('@Authorize method: [%s.%s] does not exist in class [%s]!',
                                        $sAuthorizationClass, $sAuthorizationMethod, get_class($mEndPointObject))
                                );
                            }

                            $bAccessGranted = call_user_func_array($sAuthorizationClass . '::' . $sAuthorizationMethod,
                                array($oAnnotation));

                        }

                        if (!is_bool($bAccessGranted)) {
                            throw new Exception(
                                sprintf('Incorrect return value returned by access controller annotated @Authorize in method [%s.%s] in class [%s]! Expected boolean. ',
                                    $sAuthorizationClass, $sAuthorizationMethod, get_class($mEndPointObject))
                            );
                        }

                        return $bAccessGranted;
                    },
                );

                foreach ($aAnnotationCollection[Authorize::class] as $oAnnotation) {
                    // Factory
                    if ($oAnnotation instanceof Authorize) {

                        $sAuthType = strtolower($oAnnotation->AuthType);
                        if (isset($aStrategies[$sAuthType])) {
                            $sClosure = $aStrategies[$sAuthType];
                            $bAccessGranted = $sClosure($oAnnotation);
                            if (isset($aOnAccessDeniedStrategy[$sAuthType])) {
                                if (!$bAccessGranted) {
                                    $sClosure = $aOnAccessDeniedStrategy[$sAuthType];
                                    $sClosure();
                                }
                            } else {
                                return array($bAccessGranted, $oAnnotation->ErrorMessage);
                            }
                        }
                    }
                }
            }

            return array(true, null);
        }

        /**
         * @param $oEndPointObject
         * @param null $sEndPointMethod
         * @throws \Exception
         */
        public function checkAuthorizeAnnotation($oEndPointObject, $sEndPointMethod = null)
        {

            $aOnAccessDeniedStrategy = array(
                'basic' => function () {
                    throw new Exception('Not authorized!', Status::UNAUTHORIZED);
                },
                'static' => function () {
                    throw new Exception('Access denied by @Authorize private', Status::UNAUTHORIZED);
                }
            );

            list($bResult, $sMessage) = $this->invokeAuthorizeAnnotation(
                $oEndPointObject,
                $sEndPointMethod,
                $aOnAccessDeniedStrategy);

            if (!$bResult) {
                // Default exception in case of $aOnAccessDeniedStrategy missing
                if ($sMessage !== null) {
                    throw new Exception($sMessage);

                }
                throw new Exception('Access denied by @Authorize', Status::UNAUTHORIZED);
            }
        }

        /**
         * @param $sStaticMethod
         * @return array|bool
         */
        private function getStaticMethodAndClass($sStaticMethod)
        {
            $aStaticPair = explode('.', $sStaticMethod);
            if (count($aStaticPair) != 2) {
                return false;
            }

            return $aStaticPair;
        }
    }

}
