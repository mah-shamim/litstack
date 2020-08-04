<?php

namespace Fjord\Page\Slots;

use Fjord\Vue\Components\ButtonComponent;

class ButtonSmSlot extends BaseSlot
{
    /**
     * Set action.
     *
     * @param  string          $action
     * @return ButtonComponent
     */
    public function action($action)
    {
        return parent::action($action);
    }

    /**
     * Get action compoent.
     *
     * @return ButtonComponent
     */
    protected function getActionComponent()
    {
        $component = (new ButtonComponent)->variant('outline-primary')->size('sm');

        return $this->component($component);
    }
}