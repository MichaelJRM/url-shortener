<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Services\SafeBrowsingLookupService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ShortUrlController extends Controller
{
    public function __construct(
        protected SafeBrowsingLookupService $lookupService,
    )
    {
    }

    public function index(): Application|RedirectResponse|Redirector
    {
        return redirect('/');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'urlToShort' => 'required|url'
        ]);

        $validator->after(function ($validator) use ($request) {
            $urlToShort = $request->input('urlToShort');
            [$isUrlSafe, $errorOccurred] = $this->lookupService->isUrlSafe($urlToShort);

            if ($errorOccurred) {
                $validator->errors()->add(
                    'urlToShort', 'An error occurred while shortening the url'
                );
                return;
            }

            if (!$isUrlSafe) {
                $validator->errors()->add(
                    'urlToShort', 'This url isn\'t safe!'
                );
                // Delete unsafe url
                ShortUrl::where('url', $urlToShort)->first()->delete();
            }
        });

        $validated = $validator->validated();
        $urlToShort = $validated['urlToShort'];
        $entity = ShortUrl::where('url', $urlToShort)->first();

        if (!$entity) {
            // Generate shor urls until unique one is found, if no unique url is found throws validation error.
            $i = 0;
            do {
                $i++;
                $shortenedUrl = ShortUrl::shortenUrl($urlToShort);
                $entity = ShortUrl::where('short_url', $shortenedUrl)->first();
            } while ($entity && $i < 3);

            // Couldn't create unique short url.
            if ($entity) {
                throw ValidationException::withMessages(['urlToShort' => 'An unexpected error occurred, please try again.']);
            }

            $entity = new ShortUrl;
            $entity->url = $urlToShort;
            $entity->short_url = $shortenedUrl;
            $entity->save();
        }

        return Inertia::render('Index', [
            'shortUrl' => url("short-urls/$entity->short_url"),
        ]);
    }

    public function show(string $shortUrl): RedirectResponse
    {
        $entity = ShortUrl::where('short_url', $shortUrl)->firstOrFail();
        return redirect()->away($entity->url);
    }
}
