<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserList extends Component
{
    public $title;
    public $cardClass;
    public $users;
    public $selectedIds;
    public $searchId;
    public $tableId;
    public $actionType;
    public $actionMethod;
    public $actionAllMethod;
    public $actionButtonText;
    public $actionButtonIcon;
    public $actionButtonClass;
    
    public function __construct(
        $title,
        $cardClass,
        $users,
        $selectedIds,
        $searchId,
        $tableId,
        $actionType,
        $actionMethod,
        $actionAllMethod,
        $actionButtonText,
        $actionButtonIcon,
        $actionButtonClass
    ) {
        $this->title = $title;
        $this->cardClass = $cardClass;
        $this->users = $users;
        $this->selectedIds = $selectedIds;
        $this->searchId = $searchId;
        $this->tableId = $tableId;
        $this->actionType = $actionType;
        $this->actionMethod = $actionMethod;
        $this->actionAllMethod = $actionAllMethod;
        $this->actionButtonText = $actionButtonText;
        $this->actionButtonIcon = $actionButtonIcon;
        $this->actionButtonClass = $actionButtonClass;
    }

    public function render()
    {
        return view('components.user-list');
    }
}