<?php

namespace Workhouse\Helpers\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
	/**
	 * @var
	 */

	public $title;

	/**
	 * @var
	 */

	public $result;

	public $hideHeader;
	public $hideDismissBtn;
	public $hideCancelBtn;
	public $buttons;
	public $resultSlot;

	/**
     * Create a new component instance.
     *
     * @return void
     */

	public function __construct($title, $result, $hideHeader = false, $hideDismissBtn = false, $hideCancelBtn = false, $buttons = [], $resultSlot = null)
    {
        $this->title = $title;
        $this->result = $result;
        $this->hideHeader = $hideHeader;
        $this->hideDismissBtn = $hideDismissBtn;
        $this->hideCancelBtn = $hideCancelBtn;
        $this->buttons = $buttons;
        $this->resultSlot = $resultSlot;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('modal::modal');
    }
}
