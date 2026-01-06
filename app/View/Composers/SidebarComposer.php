<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\StockOutDraft;
use App\Models\StockOpname;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get count of pending drafts for sidebar notification
        $stockOutDraftCount = StockOutDraft::count();
        $stockOpnameDraftCount = StockOpname::draft()->count();
        
        $view->with([
            'sidebarDraftCount' => $stockOutDraftCount,
            'sidebarOpnameDraftCount' => $stockOpnameDraftCount
        ]);
    }
}
