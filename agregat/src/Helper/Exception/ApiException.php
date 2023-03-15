<?php

namespace App\Helper\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Annotation\Groups;

class ApiException extends HttpException
{
    /**
     * @var string|null
     * @Groups({"show"})
     */
    protected $message;

    /**
     * @var string|null
     * @Groups({"show"})
     */
    protected $detail;

    /**
     * @var int
     * @Groups({"show"})
     */
    protected $status;

    /**
     * @var array
     * @Groups({"show"})
     */
    protected $validationError;

    public function __construct(?string $message = '',
                                ?string $detail = null,
                                array $validationError = ['query' => [], 'body' => []],
                                int $status = Response::HTTP_BAD_REQUEST,
                                HttpException $previous = null,
                                array $headers = [],
                                ?int $code = 0
    )
    {
        $this->message = is_null($message) ?
            Response::$statusTexts[$status] :
            $message;
        $this->detail = $detail;
        $this->status = $status;
        $this->validationError = $validationError;
        parent::__construct($status, $message, $previous, $headers, $code);
    }

    public function responseBody(): array
    {
        return [
            'error' => [
                'status' => $this->status,
                'message' => $this->message,
                'detail' => $this->detail,
                'validationError' => mb_convert_encoding($this->validationError, 'UTF-8',  "Windows-1251")
            ]
        ];
    }
}
