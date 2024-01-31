<?php

namespace Tests\Unit\Models;

use App\Models\Talk;
use PHPUnit\Framework\TestCase;

class TalkTest extends TestCase
{
    public function test_getForm_returns_array(): void
    {
        $form = Talk::getForm();
        
        $this->assertIsArray($form);
    }
    
    public function test_getForm_with_speakerId_returns_array(): void
    {
        $speakerId = 1;
        $form = Talk::getForm($speakerId);
        
        $this->assertIsArray($form);
    }
}
