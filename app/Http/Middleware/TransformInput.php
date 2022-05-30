<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $transformer)
    {
        $transformedInput = [];
        foreach($request->request->all() as $input => $value) {
            $transformedInput[$transformer::attributeMapper($input)] = $value;
        }
        $request->replace($transformedInput);

        $response = $next($request); // Make the middleware as after middleware

        // Check whether the response is error response or not.
        // because we only need to transform the error response and that too only Validation Error Response
        if(isset($response->exception) && $response->exception instanceof ValidationException) {
            $data = $response->getData(); // returns the data of the response, so it will have error and code as our validation had 2 keys

            $transformedErrors = [];
            foreach ($data->error as $field => $error) { // we only need to act on "error" key of the data
                $transformedField = $transformer::getTransformedAttribute($field); // get the transformed field name
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error); //replace the key and the string of error message
            }

            $data->error = $transformedErrors; // set the error object in data to the new key and error messages

            $response->setData($data); // change the response data
        }
        return $response;

    }
}
