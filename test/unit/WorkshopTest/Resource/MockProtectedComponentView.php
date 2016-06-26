<?php
namespace Workshop\FrontEnd;

use AtomPie\Gui\Component;
use AtomPie\Boundary\Gui\Component\IControlAccess;

/**
 * @property string Test
 * @property int $Status
 */
class MockProtectedComponentView extends Component implements IControlAccess
{

    /////////////////////////////////
    // Setup actions

    /**
     * Sets up references to object that will
     * be displayed within this component.
     */
    public function __create()
    {
        $this->Test = 'Default';
        $this->Status = 0;
    }

    /**
     * Returns true if access is granted.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Handle not authorized scenario. Throw Exception if you need to
     * stop dispatch process.
     *
     * @throws \AtomPie\Core\Dispatch\EndPointException
     * @return void
     */
    public function invokeNotAuthorized()
    {
        $this->Status = 1;
    }

}