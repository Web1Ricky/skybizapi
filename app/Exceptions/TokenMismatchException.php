<?php

namespace App\Exceptions;

use Exception;

class TokenMismatchException extends Exception
{
   /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }


    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response|null
     */
    public function render($request)
    {
        if ($request->wantsJson()) { // if request has `Accept: application/json` header present
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return abort(403);
    }
}
