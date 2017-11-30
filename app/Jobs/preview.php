<?php

namespace App\Jobs;

use App\Share;
use JonnyW\PhantomJs\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class preview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $share;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Share $share)
    {
        $this->share = $share;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $client = Client::getInstance();

        // Define the path to the PhantomJS executable
        $client->getEngine()->setPath(base_path('vendor/jakoch/phantomjs/bin/phantomjs'));

        // Tells the client to wait for all resources before rendering
        $client->isLazy();

        // set the width, height, x and y axis for your screen capture:
        $width = 1280;
        $height = 640;
        $top = 0;
        $left = 0;

        // Set the url to the page we want to capture
        $share = url("http://cortland.me");

        // Set the path for the image we want to save
        $file = base_path('public/images/share-images/' . $this->share->id . '.jpeg');

        $request = $client->getMessageFactory()->createCaptureRequest($share, 'GET');
        $request->setOutputFile($file);
        $request->setViewportSize($width, $height);
        $request->setCaptureDimensions($width, $height, $top, $left);

        // Set the quality of the screenshot to 100%
        $request->setQuality(100);

        // Set the format of the image
        $request->setFormat('jpeg');

        // Set a timeout to exit after 20 seconds in case something wrong happens
        $request->setTimeout(20000);

        $response = $client->getMessageFactory()->createResponse();

        $client->send($request, $response);
    }
}
