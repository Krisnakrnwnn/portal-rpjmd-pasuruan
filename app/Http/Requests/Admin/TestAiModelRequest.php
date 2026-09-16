<?php

namespace App\Http\Requests\Admin;

class TestAiModelRequest extends UpdateAiSettingsRequest
{
    // The new endpoint has no legacy clients: provider must be supplied explicitly.
    protected function prepareForValidation(): void {}
}
