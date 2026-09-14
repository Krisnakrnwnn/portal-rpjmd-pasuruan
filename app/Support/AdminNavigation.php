<?php

namespace App\Support;

use Illuminate\Support\Facades\Validator;

class AdminNavigation
{
    /** Only list state is carried between trusted, named admin routes. */
    public static function context(): array
    {
        $rules = [
            'q' => 'nullable|string|max:255',
            'status' => 'nullable|in:publik,draft,all,unread,resolved',
            'category' => 'nullable|string|max:255',
            'category_id' => 'nullable|integer|min:1',
            'page' => 'nullable|integer|min:1',
            'categories_page' => 'nullable|integer|min:1',
        ];
        $context = [];
        foreach ($rules as $key => $rule) {
            $value = request()->query($key);
            if ($value !== null && $value !== '' && Validator::make([$key => $value], [$key => $rule])->passes()) {
                $context[$key] = $value;
            }
        }

        return $context;
    }

    public static function url(string $name, mixed $parameters = []): string
    {
        return route($name, array_merge(self::context(), is_array($parameters) ? $parameters : [$parameters]));
    }
}
