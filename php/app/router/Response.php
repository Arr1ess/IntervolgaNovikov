<?php

namespace app\router;

enum ResponseCode: int
{
        // 1xx - Informational responses
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;
    case PROCESSING = 102;
    case EARLY_HINTS = 103;

        // 2xx - Successful responses
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NON_AUTHORITATIVE_INFORMATION = 203;
    case NO_CONTENT = 204;
    case RESET_CONTENT = 205;
    case PARTIAL_CONTENT = 206;
    case MULTI_STATUS = 207;
    case ALREADY_REPORTED = 208;
    case IM_USED = 226;

        // 3xx - Redirection messages
    case MULTIPLE_CHOICES = 300;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case USE_PROXY = 305;
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;

        // 4xx - Client error responses
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413;
    case URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case I_AM_A_TEAPOT = 418;
    case MISDIRECTED_REQUEST = 421;
    case UNPROCESSABLE_ENTITY = 422;
    case LOCKED = 423;
    case FAILED_DEPENDENCY = 424;
    case TOO_EARLY = 425;
    case UPGRADE_REQUIRED = 426;
    case PRECONDITION_REQUIRED = 428;
    case TOO_MANY_REQUESTS = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;

        // 5xx - Server error responses
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES = 506;
    case INSUFFICIENT_STORAGE = 507;
    case LOOP_DETECTED = 508;
    case NOT_EXTENDED = 510;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;
}

enum ContentType: string
{
    case TEXT_PLAIN = 'text/plain';
    case TEXT_HTML = 'text/html';
    case TEXT_CSS = 'text/css';
    case TEXT_CSV = 'text/csv';
    case TEXT_XML = 'text/xml';

    case APPLICATION_JSON = 'application/json';
    case APPLICATION_XML = 'application/xml';
    case APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    case MULTIPART_FORM_DATA = 'multipart/form-data';

    case IMAGE_JPEG = 'image/jpeg';
    case IMAGE_PNG = 'image/png';
    case IMAGE_GIF = 'image/gif';
    case IMAGE_SVG_XML = 'image/svg+xml';

    case AUDIO_MPEG = 'audio/mpeg';
    case VIDEO_MP4 = 'video/mp4';
    case VIDEO_WEBM = 'video/webm';

    case APPLICATION_PDF = 'application/pdf';
    case APPLICATION_MSWORD = 'application/msword';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_WORDPROCESSINGML_DOCUMENT = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case APPLICATION_VND_MS_EXCEL = 'application/vnd.ms-excel';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_SPREADSHEETML_SHEET = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case APPLICATION_VND_MS_POWERPOINT = 'application/vnd.ms-powerpoint';
    case APPLICATION_VND_OPENXMLFORMATS_OFFICEDOCUMENT_PRESENTATIONML_PRESENTATION = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    case APPLICATION_OCTET_STREAM = 'application/octet-stream';
    case APPLICATION_ZIP = 'application/zip';
    case APPLICATION_GZIP = 'application/gzip';
}

class Response
{
    private ContentType $content_type;

    public function __construct(private string|array $body = '', private array $headers = [], private ResponseCode $code = ResponseCode::OK, private array $cookies = []) {}

    public function set_content_type(ContentType $type)
    {
        $this->content_type = $type;
        return $this;
    }

    public function set_body($body)
    {
        $this->body = $body;
        return $this;
    }

    public function send()
    {
        if (isset($this->content_type)) {
            if (isset($this->headers['Content-Type'])) {
                unset($this->headers['Content-Type']);
            }
            if ($this->content_type === ContentType::APPLICATION_JSON) {
                $this->body = json_encode($this->body, JSON_NUMERIC_CHECK | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            }
            header("Content-Type: " . $this->content_type->value . ";charset=UTF-8");
        }

        http_response_code($this->code->value);
        foreach ($this->headers as $key => $value) {
            header($key . ": " . $value);
        }
        foreach ($this->cookies as $key => $value) {
            setcookie($key, $value);
        }
        echo $this->body;
        exit;
    }

    public static function error($message= "", ResponseCode $code = ResponseCode::INTERNAL_SERVER_ERROR): Response
    {
        return (new Response(body: ["error" => $message], code: $code))->set_content_type(ContentType::APPLICATION_JSON);
    }

    public static function json($data, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $data, code: $code))->set_content_type(ContentType::APPLICATION_JSON);
    }

    public static function html(string $html, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $html, code: $code))->set_content_type(ContentType::TEXT_HTML);
    }

    public static function plainText(string $text, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $text, code: $code))->set_content_type(ContentType::TEXT_PLAIN);
    }

    public static function css(string $css, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $css, code: $code))->set_content_type(ContentType::TEXT_CSS);
    }

    public static function csv(string $csv, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $csv, code: $code))->set_content_type(ContentType::TEXT_CSV);
    }

    public static function xml(string $xml, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $xml, code: $code))->set_content_type(ContentType::TEXT_XML);
    }

    public static function pdf(string $pdfContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $pdfContent, code: $code))->set_content_type(ContentType::APPLICATION_PDF);
    }

    public static function jpeg(string $jpegContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $jpegContent, code: $code))->set_content_type(ContentType::IMAGE_JPEG);
    }

    public static function png(string $pngContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $pngContent, code: $code))->set_content_type(ContentType::IMAGE_PNG);
    }

    public static function gif(string $gifContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $gifContent, code: $code))->set_content_type(ContentType::IMAGE_GIF);
    }

    public static function svg(string $svgContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $svgContent, code: $code))->set_content_type(ContentType::IMAGE_SVG_XML);
    }

    public static function mp4(string $mp4Content, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $mp4Content, code: $code))->set_content_type(ContentType::VIDEO_MP4);
    }

    public static function webm(string $webmContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $webmContent, code: $code))->set_content_type(ContentType::VIDEO_WEBM);
    }

    public static function mpeg(string $mpegContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $mpegContent, code: $code))->set_content_type(ContentType::AUDIO_MPEG);
    }

    public static function zip(string $zipContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $zipContent, code: $code))->set_content_type(ContentType::APPLICATION_ZIP);
    }

    public static function gzip(string $gzipContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $gzipContent, code: $code))->set_content_type(ContentType::APPLICATION_GZIP);
    }

    public static function octetStream(string $octetStreamContent, ResponseCode $code = ResponseCode::OK): Response
    {
        return (new Response(body: $octetStreamContent, code: $code))->set_content_type(ContentType::APPLICATION_OCTET_STREAM);
    }
}
