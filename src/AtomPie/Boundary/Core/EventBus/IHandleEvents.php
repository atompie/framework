<?php
namespace AtomPie\Boundary\Core\EventBus;

use AtomPie\DependencyInjection\Boundary\IConstructInjection;

interface IHandleEvents
{
    /**
     * Adds event listener. Listener will listen to $sEvent (on current object) and will
     * call $mListenerCallBack if event occurs.
     *
     * @param    string
     * @param    \Closure closure
     * @return    int        Event listener ID
     */
    public function handleEvent($sEvent, \Closure $pClosure);

    /**
     * Removes listener from the raiser object. If you want to remove one example of an obejst not all
     * listeners pass $iEventId. Event Id is returned by handleEvent.
     *
     * @param    string
     * @param    integer
     * @return    void
     */
    public function removeEventHandler($sEvent, $iEventId = null);

    /**
     * Raises event and spreads the new about it to all listeners.
     *
     * @param    string
     * @param    mixed
     * @param   IEventable
     * @return    void
     */
    public function triggerEvent($sEvent, $aParams = null, $oSender = null);

    /**
     * @param IConstructInjection $oDependencyContainer
     * @param $sClosureId
     * @param $sEvent
     */
    public function triggerDependentEvent(
        IConstructInjection $oDependencyContainer,
        $sClosureId,
        $sEvent
    );

    /**
     * Checks if event is in event queue.
     *
     * @param string $sEvent
     */
    public function hasEventHandler($sEvent);
}