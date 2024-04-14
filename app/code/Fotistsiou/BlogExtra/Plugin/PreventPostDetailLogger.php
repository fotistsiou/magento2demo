<?php

namespace Fotistsiou\BlogExtra\Plugin;

use Fotistsiou\Blog\Observer\LogPostDetailView;

class PreventPostDetailLogger
{
    public function aroundExecute (
        LogPostDetailView $subject,
        callable $proceed
    ) {
        // Don't do anything to prevent logger from executing
    }
}
