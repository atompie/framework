<?php
namespace AtomPie\Boundary\Core\Dispatch;

interface IAmDispatchManifest extends IHaveEndPointSpec, IHaveEventSpec
{

    /**
     * @param $sComponentType
     * @param $sComponentName
     * @param $sEvent
     * @return IAmEventSpecImmutable
     */
    public function newEventSpec(
        $sComponentType,
        $sComponentName,
        $sEvent
    );

    /**
     * @return string
     */
    public function __toString();
}