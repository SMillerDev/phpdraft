<?php

namespace PHPDraft\Out\OpenAPI;

use PHPDraft\Model\HTTPRequest;
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

    private function getServers(): array {
        $return = [];
        $return[] = ['url' => $this->base_data['HOST'], 'description' => 'Main host'];

        foreach (explode(',', $this->base_data['ALT_HOST'] ?? '') as $host) {
            $return[] = ['url' => $host];
        }

        return $return;
    }
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

    private function toBody(HTTPRequest $request): array
    {
        $return = [];

        if (!is_array($request->struct)) {
            $return['description'] = $request->struct->description;
        }

        $content_type = $request->headers['Content-Type'] ?? 'text/plain';
        if (!empty($request->struct))
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

    private function getWebhooks(): object { return new stdClass(); }
    private function getComponents(): object { return new stdClass(); }
    private function getSecurity(): array { return []; }
    private function getTags(): array { return []; }

//    private function getDocs(): object { return new \stdClass(); }

}