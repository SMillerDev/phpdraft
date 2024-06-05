<?php

namespace PHPDraft\Out\OpenAPI;

use PHPDraft\Model\HTTPRequest;
use PHPDraft\Model\HTTPResponse;
use PHPDraft\Out\BaseTemplateRenderer;
use stdClass;

class OpenApiRenderer extends BaseTemplateRenderer {

    public function init(object $json): self
    {
        $this->object = $json;

        return $this;
    }

    public function write(string $filename): void
    {
        $output = json_encode($this->toOpenApiObject(), JSON_PRETTY_PRINT);
        file_put_contents($filename, $output);
    }

    /**
     * Get OpenAPI base structure.
     *
     * @return array<string,object|array<string,mixed>|string[]>
     */
    private function toOpenApiObject(): array
    {
        $this->parse_base_data($this->object);

        return [
                'openapi' => '3.1.0',
//                'jsonSchemaDialect' => '',
                'info' => $this->getApiInfo(),
                'servers' => $this->getServers(),
                'paths' => $this->getPaths(),
                'webhooks' => $this->getWebhooks(),
                'components' => $this->getComponents(),
                'security' => $this->getSecurity(),
                'tags' => $this->getTags(),
//                'externalDocs' => $this->getDocs(),
        ];
    }

    /**
     * Get generic info for the API
     * @return array<string,string>
     */
    private function getApiInfo(): array {
        return [
            "title"=> $this->base_data['TITLE'],
            "version"=> $this->base_data['VERSION'] ?? '1.0.0',
            "summary"=> $this->base_data['TITLE'] . ' generated from API Blueprint',
            "description"=> $this->base_data['DESC'],
//            "termsOfService"=> "https://example.com/terms/",
//            "contact"=> [
//                "name"=> "API Support",
//                "url"=> "https://www.example.com/support",
//                "email"=> "support@example.com"
//           ],
//            "license" => [
//                    "name"=> "Apache 2.0",
//                    "url"=> "https://www.apache.org/licenses/LICENSE-2.0.html"
//            ],
        ];
    }

    /**
     * Get information about the servers involved in the API.
     *
     * @return array<array<string,string>>
     */
    private function getServers(): array {
        $return = [];
        $return[] = ['url' => $this->base_data['HOST'], 'description' => 'Main host'];

        foreach (explode(',', $this->base_data['ALT_HOST'] ?? '') as $host) {
            $return[] = ['url' => $host];
        }

        return $return;
    }

    /**
     * Get path information
     *
     * @return object
     */
    private function getPaths(): object {
        $return = [];
        foreach ($this->categories as $category) {
            foreach ($category->children ?? [] as $resource) {
                foreach ($resource->children ?? [] as $transition) {
                    $transition_return = [];
                    $transition_return['parameters'] = [];
                    if ($transition->url_variables !== []) {
                        $transition_return['parameters'] = $this->toParameters($transition->url_variables, $transition->href);
                    }

                    foreach ($transition->requests as $request) {
                        $request_return = [
                                'operationId' => $request->get_id(),
                                'responses' => $this->toResponses($transition->responses),
                        ];
                        if (isset($transition_return['parameters']) && $transition_return['parameters'] !== []) {
                            $request_return['parameters'] = $transition_return['parameters'];
                        }
                        if ($request->body !== NULL) {
                            $request_return['requestBody'] = $this->toBody($request);
                        }

                        if ($request->title !== NULL) {
                            $request_return['summary'] = $request->title;
                        }
                        if ($request->description !== '') {
                            $request_return['description'] = $request->description;
                        }

                        $transition_return[strtolower($request->method)] = (object) $request_return;
                    }
                    $return[$transition->href] = (object) $transition_return;
                }
            }
        }

        return (object) $return;
    }

    /**
     * Convert objects into parameters.
     *
     * @param object[] $objects List of objects to convert
     * @param string   $href    Base URL
     *
     * @return array<array<string,mixed>>
     */
    private function toParameters(array $objects, string $href): array {
        $return = [];

        foreach ($objects as $variable) {
            $return_tmp = [
                    'name' => $variable->key->value,
                    'in'   => str_contains($href, '{' . $variable->key->value . '}') ? 'path' : 'query',
                    'required' => $variable->status === 'required',
                    'schema' => [
                            'type' => $variable->type,
                    ],
            ];

            if (isset($variable->value))
            {
                $return_tmp['example'] = $variable->value;
            }

            if (isset($variable->description))
            {
                $return_tmp['description'] = $variable->description;
            }
            $return[] = $return_tmp;
        }

        return $return;
    }

    /**
     * Convert a HTTP Request into an OpenAPI body
     *
     * @param HTTPRequest $request Request to convert
     *
     * @return array<string,array<string,mixed>> OpenAPI style body
     */
    private function toBody(HTTPRequest $request): array
    {
        $return = [];

        if (!is_array($request->struct)) {
            $return['description'] = $request->struct->description;
        }

        $content_type = $request->headers['Content-Type'] ?? 'text/plain';
        if (isset($request->struct) && $request->struct !== [])
        {
            $return['content'] = [
                    $content_type => [
                            'schema' => [
                                'type' => $request->struct->element,
                                'properties' => array_map(fn($value) => [$value->key->value => ['type' => $value->type]], $request->struct->value),
                            ],
                    ],
            ];
        } else {
            $return['content'] = [
                    $content_type => [
                            'schema' => [
                                    'type' => 'string',
                            ],
                    ],
            ];
        }

        if ($request->body !== NULL && $request->body !== []) {
            $return['content'][$content_type]['example'] = $request->body[0];
        }

        return $return;
    }

    /**
     * Convert responses to the OpenAPI structure
     *
     * @param HTTPResponse[] $responses List of responses to parse
     *
     * @return array<int,array<string,mixed>> List of status codes with the response
     */
    private function toResponses(array $responses): array
    {
        $return = [];

        foreach ($responses as $response) {
            $headers = [];
            foreach ($response->headers as $header => $value) {
                if ($header === 'Content-Type') { continue; }
                $headers[$header] = [
                    'schema' => [
                        'type' => 'string',
                        'example' => $value,
                    ]
                ];
            }

            $content = [];
            foreach ($response->content as $key => $contents) {
                $content[$key] = [
                        "schema"=> [
                                "type"=> "string",
                                "example"=> $contents
                        ]
                ];
            }
            foreach ($response->structure as $structure) {
                if ($structure->key === NULL) { continue; }
                $content[$response->headers['Content-Type'] ?? 'text/plain'] = [
                        "schema"=> [
                                "type"=> "object",
                                "properties"=> [
                                        $structure->key->value => [
                                                "type" => $structure->type,
                                                'example' => $structure->value,
                                        ]
                                ]
                        ]
                ];
            }
            $return[$response->statuscode] = [
                    'description' => $response->description ?? $response->title ?? '',
                    'headers' => (object) $headers,
                    'content' => (object) $content,
            ];
        }

        return $return;
    }

    /**
     * Get webhook information for the API.
     * @return object
     */
    private function getWebhooks(): object { return (object) []; }

    /**
     * Get component information for the API.
     * @return object
     */
    private function getComponents(): object { return (object) []; }

    /**
     * Get security information for the API
     * @return string[]
     */
    private function getSecurity(): array { return []; }

    /**
     * Get tags for the API
     * @return string[]
     */
    private function getTags(): array { return []; }

//    private function getDocs(): object { return (object) []; }

}