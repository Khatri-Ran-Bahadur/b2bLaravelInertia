<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomPaginationHelper
{
    /**
     * Format the pagination response with custom fields.
     *
     * @param LengthAwarePaginator $paginator
     * @param string $imageBaseUrl
     * @return \Illuminate\Http\JsonResponse
     */
    public static function data(LengthAwarePaginator $paginator)
    {


        // Return formatted response
        return [
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'first_page_url' => $paginator->url(1),
            'last_page_url' => $paginator->url($paginator->lastPage()),
            'next_page_url' => $paginator->nextPageUrl(),
            'prev_page_url' => $paginator->previousPageUrl(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'links' => self::generateLinks($paginator)
        ];
    }

    /**
     * Generate the pagination links array.
     *
     * @param LengthAwarePaginator $paginator
     * @return array
     */
    private static function generateLinks(LengthAwarePaginator $paginator)
    {
        $links = [];

        // Previous link
        $links[] = [
            'url' => $paginator->previousPageUrl(),
            'label' => '&laquo; Previous',
            'active' => $paginator->onFirstPage() ? false : true,
        ];

        // Page numbers
        foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url) {
            $links[] = [
                'url' => $url,
                'label' => (string)$page,
                'active' => $page == $paginator->currentPage(),
            ];
        }

        // Next link
        $links[] = [
            'url' => $paginator->nextPageUrl(),
            'label' => 'Next &raquo;',
            'active' => $paginator->hasMorePages() ? true : false,
        ];

        return $links;
    }
}
