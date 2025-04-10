<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use \Illuminate\Http\Response as Res;

class ApiController extends Controller
{
    //
    public $response;

    public $per_page = 10;
    public $one_day = 60 * 60 * 24;
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = [
            'title' => env('APP_NAME'),
            'message' => '',
            'status' => 'success',
            'localized_key' => '',
            'data' => (object)[]
        ];
    }


    /**
     * @var int
     */
    protected $statusCode = Res::HTTP_OK;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function authorizeUser($request) {}

    /**
     * @param $statusCode
     * @return ApiController response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    public function respondSuccess($message)
    {
        $this->response['message']  =   $message;
        $this->response['data']     = '';
        return $this->respond($this->response);
    }

    public function detailRespond($result)
    {
        $this->response['message']  =   'Fetch Successful';
        $this->response['data']     = $result;
        return $this->respond($this->response);
    }

    public function collectionWithoutRespond($result)
    {
        $this->response['message']  =   'Fetch Successful';
        $this->response['data']     =   [
            'items' => $result,
            'totalPages' => 0,
            'currentPage' => 0,
            'total' => 0,
            'perPage' => 0,
            'count' => $result->count()
        ];
        return $this->respond($this->response);
    }

    public function collectionRespond($result)
    {
        $this->response['message']  =   'Fetch Successful';
        $this->response['data']     =   [
            'items' => $result,
            'totalPages' => $result->lastPage(),
            'currentPage' => $result->currentPage(),
            'total' => $result->total(),
            'perPage' => $result->perPage(),
            'count' => $result->count()
        ];
        return $this->respond($this->response);
    }

    public function respond($data, $code = Res::HTTP_OK, $headers = [])
    {

        $data['status_code'] = $code;
        $data['status'] = Res::$statusTexts["$code"];
        return response()->json($data, $code, $headers);
    }

    public function respondNotFound($message = 'Not Found!')
    {
        return $this->respond([
            'message' => $message,
            'status' => 'error',
            'status_code' => Res::HTTP_NOT_FOUND,
        ], Res::HTTP_NOT_FOUND);
    }

    public function somethingWentWrong()
    {
        return $this->respond([
            'message' => "Something went wrong. Please try again.",
            'status' => Res::$statusTexts[Res::HTTP_INTERNAL_SERVER_ERROR],
            'status_code' => Res::HTTP_INTERNAL_SERVER_ERROR,
        ], Res::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function respondValidationError($errors)
    {
        return $this->respond([
            'status' => 'error',
            'status_code' => Res::HTTP_UNPROCESSABLE_ENTITY,
            'errors' => $errors
        ], Res::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function respondWithError($message)
    {
        return $this->respond([
            'message' => $message,
            'status' => 'error',
            'status_code' => Res::HTTP_UNAUTHORIZED,
        ], Res::HTTP_UNAUTHORIZED);
    }

    public function respondWithException($message, $file = '', $line = '')
    {
        return $this->respond([
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'status' => 'error',
            'status_code' => Res::HTTP_SERVICE_UNAVAILABLE
        ], Res::HTTP_SERVICE_UNAVAILABLE);
    }

    public function getRememberKey()
    {
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}" . app()->getLocale();
        return $rememberKey = sha1($fullUrl);
    }

    public function get_full_url()
    {
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $fullUrl = "{$url}?{$queryString}" . app()->getLocale();
        return $rememberKey = sha1($fullUrl);
    }
}
