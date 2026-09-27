<?php

namespace Tests;

use App\Models\HomepageContent;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setHomepageContent(array $attributes): HomepageContent
    {
        $content = HomepageContent::query()->firstOrFail();
        $content->update($attributes);

        return $content;
    }
}
