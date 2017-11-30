<?php

// App\Http\Controllers\ShareController.php

use App\Share;
use Illuminate\Http\Request;
use App\Jobs\GeneratePreview;

public function create(Request $request)
{
    $share = Share::create([
        // …
    ]);

    dispatch(new GeneratePreview($share));
}
